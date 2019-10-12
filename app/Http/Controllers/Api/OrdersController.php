<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-9-6
 * Time: 下午12:54
 */

namespace App\Http\Controllers\Api;

use App\Models\FreightTemplate;
use App\Models\GoodsSpecification;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\ShoppingCart;
use App\Transformers\OrderTransformer;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    // 1待付款，2待发货，3待收货，4已完成

    public function index(Request $request)
    {
        $query = Order::query();
        if ($request->status) {
            $query->whereIn('status', $request->status);
        }
        if ($request->order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }

        $list = $query->isMine()->recent()->paginate($request->per_page);
        return $this->response->paginator($list, new OrderTransformer());
    }

    // 下单
    public function store(Request $request)
    {
        $from_shopping_cart_ids = $request->from_shopping_cart_ids ?? [];
        if ($request->remarks && mb_strlen($request->remarks) > 100) {
            return $this->response->errorBadRequest('备注信息不可超过100字符');
        }

        if (! $request->address_id || ! $address = $this->user->addresses()->where('id', $request->address_id)->first()) {
            return $this->response->errorBadRequest('请选择配送地址');
        }
        // 获取运费计算模板，没有则非配送范围
        if (! $freightTemplate = FreightTemplate::getTemplate($address)) {
            return $this->response->errorBadRequest('当前地址不在配送范围，请重新选择地址');
        }

        $goods_ids_list = $request->goods_list;
        if (blank($goods_ids_list) || ! is_array($goods_ids_list)) {
            return $this->response->errorBadRequest('下单商品不能为空');
        }
        $goods_specification_id2info = [];
        foreach ($goods_ids_list as $goods) {
            if (! isset($goods['goods_id']) || ! is_numeric($goods['goods_id']) || $goods['goods_id'] <= 0) {
                return $this->response->errorBadRequest('存在商品id不合法');
            }
            if (! isset($goods['goods_specification_id']) || ! is_numeric($goods['goods_specification_id']) || $goods['goods_specification_id'] <= 0) {
                return $this->response->errorBadRequest('存在商品规格id不合法');
            }
            if (! isset($goods['quantity']) || ! is_numeric($goods['quantity']) || $goods['quantity'] <= 0) {
                return $this->response->errorBadRequest('存在商品购买数量不合法');
            }

            $goods_specification_id2info[$goods['goods_specification_id']] = $goods;
        }

        // 排它锁 锁表 lockForUpdate
        $goods_specification_list = GoodsSpecification::with(['goods'])
            ->enabled()
            ->whereIn('id', array_keys($goods_specification_id2info))
            ->get();
        if ($goods_specification_list->count() !== count($goods_specification_id2info)) {
            return $this->response->errorBadRequest('存在商品规格不合法');
        }
        $goods_specification_by_key_list = $goods_specification_list->keyBy('id');

        //TODO: 验证优惠券

        $order_goods = [];
        $all_goods_price = 0;
        $goods_count = 0;
        $goods_weight = 0;
        $sub_quantity = [];

        foreach ($goods_ids_list as $info) {
            $goods_id = intval($info['goods_id']);
            $specification_id = $info['goods_specification_id'];
            $specification = $goods_specification_by_key_list[$specification_id];
            if ($specification->goods_id != $goods_id) {
                // 回滚数据
                return $this->response->errorBadRequest('存在商品规格不合法');
            }
            $goods = $specification->goods;
            $now_quantity = $specification->quantity;
            if ($info['quantity'] > $now_quantity) {
                // 存在商品库存不够，回滚
                if ($now_quantity) {
                    return $this->response->errorBadRequest("商品【{$goods->title}（{$specification->title}）】库存紧剩{$now_quantity}件, 不足{$info['quantity']}件，请重新调整数量后下单");
                } else {
                    return $this->response->errorBadRequest("商品【{$goods->title}（{$specification->title}）】已售罄");
                }
            }

            $goods_price = $specification->price;

            //商品库存待删减
            $sub_quantity[] = [
                'model' => $specification,
                'quantity' => $info['quantity']
            ];

            $order_goods_item = [
                'goods_id' => $goods_id,
                'goods_specification_id' => $specification_id,
                // 快照记录
                'snapshot' => $specification->toSnapshot(),
                'price' => $goods_price,
                'quantity' => $info['quantity'],
            ];

            $order_goods[] = $order_goods_item;
            $all_goods_price += $goods_price * $info['quantity'];
            $goods_count += $info['quantity'];
            $goods_weight += $specification->weight * $info['quantity'];

        }

        $goods_weight = floatval(strval($goods_weight));
        $goods_count = intval(strval($goods_count));
        $all_goods_price = intval(strval($all_goods_price));

        // 根据地区计算配送费和运费模板
        $freight_price = $freightTemplate->compute($goods_weight, $goods_count, $all_goods_price);

        //TODO: 优惠金额
        $coupon_price = 0;

        // 总费用
        $all_price = $all_goods_price + $freight_price;
        // 支付费用
        $real_price = $all_price - $coupon_price;

        $order = new Order();
        $order->order_number = Order::generateOrderNumber();
        $order->user_id = $this->user->id;
        $order->goods_price = $all_goods_price;
        $order->freight_price = $freight_price;
        $order->all_price = $all_price;
        $order->coupon_price = $coupon_price;
        $order->real_price = $real_price;
        $order->goods_count = $goods_count;
        $order->goods_weight = $goods_weight;
        $order->status = 1;
        $order->remarks = $request->remarks ?: '';
        $order->address_id = $request->address_id;
        // 运费模板
        $order->freight_template_id = $freightTemplate->id;

        DB::beginTransaction();

        try {
            // 减库存
            foreach ($sub_quantity as $item) {
                $res = $item['model']->sold($item['quantity']);
                if ($res === false) {
                    DB::rollBack();
                    $specification = GoodsSpecification::find($item['model']->id);
                    if ($specification->quantity) {
                        return $this->response->errorBadRequest("商品【{$specification->goods->title}（{$specification->title}）】库存紧剩{$specification->quantity}件, 不足{$item['quantity']}件，请重新调整数量后下单");
                    } else {
                        return $this->response->errorBadRequest("商品【{$specification->goods->title}（{$specification->title}）】已售罄");
                    }
                }
            }

            $order->save();

            $order->orderGoods()->createMany($order_goods);

            // 清购物车
            if ($from_shopping_cart_ids) {
                ShoppingCart::isMine()->whereIn('id', $from_shopping_cart_ids)->delete();
            }

            // 测试计算
            $test = intval($request->test);
            if ($test === 1) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            // 订单记录日志
//            dispatch(new RecordOrderLog($order, $this->user, 1, $request->getClientIp()));

            return $this->response->item($order, new OrderTransformer())->statusCode(201);
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->response->errorBadRequest($exception->getMessage());
        }
    }

    public function refunds(Request $request)
    {
        $query = OrderRefund::query();

        if ($request->refund_order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }


        $list = $query->where('order_user_id', \Auth::user()->id)->recent()->paginate($request->per_page);
        return $this->response->paginator($list, new OrderRefundTransformer());
    }

    public function refundShow(OrderRefund $orderRefund)
    {
        $this->authorize('is-mine', $orderRefund->order);
        return $this->response->item($orderRefund, new OrderRefundTransformer());
    }

    public function show(Order $order)
    {
        $this->authorize('is-mine', $order);
        return $this->response->item($order, new OrderTransformer());
    }

    public function logs(Order $order)
    {
        $this->authorize('my-order', $order);
        return $this->response->collection($order->orderLogs, new OrderLogTransformer());
    }

    /**
     * 取消订单
     * @param Order $order
     * @author klinson <klinson@163.com>
     * @return \Dingo\Api\Http\Response|void
     */
    public function cancel(Order $order)
    {
        $this->authorize('is-mine', $order);

        if ( $order->status !== 1) {
            return $this->response->errorBadRequest('订单状态不可取消');
        }

        try {
            $order->status = 5;
            $order->save();

            // 加库存

            // 订单记录日志

            return $this->response->item($order, new OrderTransformer());
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }

    }

    /**
     * 订单支付
     * @param Order $order
     * @param Request $request
     * @author klinson <klinson@163.com>
     * @return \Dingo\Api\Http\Response|void
     */
    public function pay(Order $order, Request $request)
    {
        $this->authorize('is-mine', $order);

        if ($order->status !== 1) {
            return $this->response->errorBadRequest('订单无法支付，请查看订单状态');
        }

        // 使用余额抵扣，只能全额抵扣
        $balance = intval($request->balance);
        if ($balance) {
            if ($this->user->wallet->balance < $balance) {
                return $this->response->errorBadRequest('用户余额不足，无法支付');
            }
            if ($balance > $order->real_price) {
                return $this->response->errorBadRequest('输入余额超过订单金额，请重试');
            }

            if ($balance === $order->real_price) {
                // 直接支付成功
                DB::beginTransaction();
                try {
                    $this->user->wallet->decrement('balance', $balance);
                    $this->user->wallet->log($balance, "支付订单（{$order->order_number}）");
                    $order->pay($balance);
                    DB::commit();
                    return $this->response->item($order, new OrderTransformer());
                } catch (\Exception $exception) {
                    DB::rollback();
                    Log::error("[wallet][payment][pay][{$order->order_number}]支付失败：[{$exception->getMessage()}]{$exception->getFile()}.{$exception->getLine()}");
                    return $this->response->errorBadRequest('支付失败');
                }
            } else {
                return $this->response->errorBadRequest('请输入正确的余额金额');
            }
        }



        if (config('app.env') !== 'local') {
            $order->real_cost = $order->real_price;
            $order->used_balance = 0;

            $app = app('wechat.payment');
            $config = $app->getConfig();
            $order_title = "【".config('app.name')."】订单：{$order->order_number}";
            $result = $app->order->unify([
                'body' => $order_title,
                'out_trade_no' => $order->order_number,
                'total_fee' => $order->real_cost,
                'trade_type' => 'JSAPI',
                'spbill_create_ip' => $request->getClientIp(),
                'openid' => \Auth::user()->wxapp_openid,
            ]);

//        dd($result);
            Log::info("[wechat][payment][pay][{$order->order_number}]微信支付下单返回：" . json_encode($result, JSON_UNESCAPED_UNICODE));
            if (($result['return_code'] ?? 'FAIL') == 'FAIL') {
                Log::error("[wechat][payment][pay][{$order->order_number}]微信支付下单失败：[{$result['return_code']}]{$result['return_msg']}");
                return $this->response->errorBadRequest('支付失败，' . $result['return_msg']);
            }

            if (($result['result_code'] ?? 'FAIL') == 'FAIL') {
                Log::error("[wechat][payment][pay][{$order->order_number}]微信支付下单失败：[{$result['err_code']}]{$result['err_code_des']}");
                return $this->response->errorBadRequest('支付失败，' . $result['err_code_des']);
            }

            $timestamp = time();
            $return = [
                'trade_type' => $result['trade_type'],
                'prepay_id' => $result['prepay_id'],
                'nonce_str' => $result['nonce_str'],
                'timestamp' => $timestamp,
                'sign_type' => 'MD5',
                'sign' => generate_wechat_payment_md5_sign($config['app_id'], $result['nonce_str'], $result['prepay_id'], $timestamp, $config['key']),
//            'mch_id' => $result['mch_id'],
                'appid' => $result['appid'],
                'order_number' => $order->order_number,
                'order_title' => $order_title,
                'order_price' => $order->real_cost,
            ];

            // 日志
//            dispatch(new RecordOrderLog($order, $this->user, 15, $request->getClientIp()));

            return $this->response->array($return);
        } else {
            $order->pay();
            return $this->response->item($order, new OrderTransformer());
        }
    }

    // 确认收到货
    public function receive(Order $order, Request $request)
    {
        $this->authorize('is-mine', $order);
        if ($order->status !== 3) {
            return $this->response->errorBadRequest('订单无法确认，请查看订单状态');
        }
        $order->receive();

        // TODO: 日志

        // TODO: 一定时间后订单结算收益

        return $this->response->item($order, new OrderTransformer());
    }

    // 评论
    public function comment(Order $order, OrderGoods $orderGoods, Request $request)
    {
        $this->authorize('is-mine', $order);
        if ($orderGoods->order_id !== $order->id) {
            return $this->response->errorBadRequest('不存在该订单商品');
        }
        if ($order->status !== 5) {
            return $this->response->errorBadRequest('订单未完成');
        }
        if (empty($request->get('content'))) {
            return $this->response->errorBadRequest('评论内容不能为空');
        }
        if (strlen($request->get('content')) > 250) {
            return $this->response->errorBadRequest('评论内容最多250个字符');
        }
        if ($orderGoods->comments->where('user_id', '<>', 0)->count() >= 2) {
            return $this->response->errorBadRequest('只能进行一次追评');
        }
        if ($orderGoods->comments->isEmpty()) {
            if ($request->score < 1 || $request->score > 5) {
                return $this->response->errorBadRequest('请选择评分');
            }
            $score = $request->score;
        } else {
            $score = 0;
        }
        $orderGoods->comments()->create([
            'user_id' => $this->user->id,
            'order_id' => $order->id,
            'goods_id' => $orderGoods->goods_id,
            'goods_specification_id' => $orderGoods->goods_specification_id,
            'content' => $request->get('content'),
            'score' => $score,
        ]);

        dispatch(new RecordOrderLog($order, \Auth::user(), 16, $request->getClientIp()));
        // 通知街道街道配送员
        if (check_model($order->leaderModel)) {
            $order->leaderModel->notify(new OrderNotification($order, 7, 'user_to'));
        }
        // 通知城主
        if (check_model(\Auth::user()->castellan)) {
            \Auth::user()->castellan->notify(new OrderNotification($order, 7, 'user_to'));
        }

        return $this->response->noContent();
    }

    public function comments(Request $request)
    {
        $list = OrderGoodsComment::isMine()->recent()->where('score', '>', 0)->paginate($request->per_page);
        return $this->response->paginator($list, new OrderGoodsCommentTransformer());
    }

    public function commentShow(Order $order, OrderGoods $orderGoods, Request $request)
    {
        $this->authorize('is-mine', $order);
        if ($orderGoods->order_id !== $order->id) {
            return $this->response->errorBadRequest('不存在该订单商品');
        }
        if ($order->status !== 5) {
            return $this->response->errorBadRequest('订单未完成');
        }
//        if ($orderGoods->is_refund === 1) {
//            return $this->response->errorBadRequest('订单已退款');
//        }
        return $this->response->collection($orderGoods->comments, new OrderGoodsCommentTransformer());
    }

    // 退款操作
    public function refund(Order $order, OrderGoods $orderGoods, Request $request)
    {
        $this->authorize('is-mine', $order);

        if (! $order->timeCanRefund()) {
            return $this->response->errorBadRequest('订单已经过了退款时间，不可以退款');
        }
        if ( $order->status < 2) {
            return $this->response->errorBadRequest('订单未付款，不可以退款');
        }
        if ($orderGoods->order->id !== $order->id) {
            return $this->response->errorBadRequest('订单不存在该商品');
        }
        if ($orderGoods->is_refund > 0) {
            return $this->response->errorBadRequest('该商品已经申请退款了');
        }

        if (empty($request->reason)) {
            return $this->response->errorBadRequest('请输入退款理由');
        }
        if (empty($request->images) || ! is_array($request->images)) {
            return $this->response->errorBadRequest('请上传退款说明图片');
        }
        if (strlen($request->reason) > 250) {
            return $this->response->errorBadRequest('退款理由最长250字符');
        }

        // 记录退款
        $orderRefund = new OrderRefund();
        $orderRefund->order_number = generate_order_number();
        $orderRefund->order_id = $order->id;
        $orderRefund->goods_id = $orderGoods->goods_id;
        $orderRefund->goods_specification_id = $orderGoods->goods_specification_id;
        $orderRefund->order_goods_id = $orderGoods->id;
        $orderRefund->price = $orderGoods->price;
        $orderRefund->quantity = $orderGoods->quantity;
        $orderRefund->reason = $request->reason;
        $orderRefund->images = $request->images;
        $orderRefund->order_user_id = $order->user_id;
        $orderRefund->operation_user_id = $this->user->id;

        $orderRefund->refund_price = $orderGoods->real_price;

        // 默认申请退款
        $orderRefund->status = 1;
        $orderRefund->save();

        // 标记申请退款
        $orderGoods->is_refund = 1;
        $orderGoods->save();

        // 订单总价调整
//        $order->decrement('price', $orderGoods->quantity * $orderGoods->price);
//        $order->save();

        dispatch(new RecordOrderLog($order, $this->user, 10, $request->getClientIp()));

        if (check_model($order->leaderModel)) {
            $order->leaderModel->notify(new OrderNotification($order, 8, 'leader'));
        }
        // 通知城主
        if (check_model(\Auth::user()->castellan)) {
            \Auth::user()->castellan->notify(new OrderNotification($order, 8, 'castellan'));
        }

        return $this->response->noContent();

    }

    public function destroy(Order $order)
    {

    }

    // 统计数据
    public function statistics()
    {
        $res = Order::statusCount($this->user->id);

        return $this->response->array($res);
    }

}