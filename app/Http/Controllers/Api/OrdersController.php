<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 18-9-6
 * Time: 下午12:54
 */

namespace App\Http\Controllers\Api;

use App\Models\Address;
use App\Models\DiscountGoods;
use App\Models\Express;
use App\Models\FreightTemplate;
use App\Models\GoodsSpecification;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\ShoppingCart;
use App\Models\Store;
use App\Models\User;
use App\Models\UserHasCoupon;
use App\Transformers\OrderTransformer;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Intervention\Image\Point;

class OrdersController extends Controller
{
    // 1待付款，2待发货，3待收货，4已完成

    public function index(Request $request)
    {
        $query = Order::query();
        // 订单类型：1普通订单，2团购下单，3线下下单（book）
        if ($request->order_type) {
            $query->where('order_type', $request->order_type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }

        $list = $query->isMine()->recent()->paginate($request->per_page);
        return $this->response->paginator($list, new OrderTransformer());
    }

    /**
     * 下单
     * 费用计算金额
     * 原总价all_price=商品总价goods_price+运费freight_price
     * 会员优惠价member_discount_price=商品总价goods_price*会员折扣member_discount*0.01
     * 优惠券可打折部分allow_coupon_price=会员优惠价member_discount_price（优惠券只能抵扣会员折扣后优惠价格，不能抵扣运费）
     * 优惠券抵扣金额coupon_price=优惠券可打折部分allow_coupon_price进行满减或打折后
     * 实付金额real_price=会员优惠价member_discount_price-优惠券抵扣金额coupon_price+运费freight_price-使用积分used_integral*积分汇率
     * 最后是使用钱包就是used_balance，使用微信支付就是real_cost
     * @param Request $request
     * @return \Dingo\Api\Http\Response|void
     * @author klinson <klinson@163.com>
     */
    public function store(Request $request)
    {
        $from_shopping_cart_ids = $request->from_shopping_cart_ids ?? [];
        $test = intval($request->test);
        if ($request->remarks && mb_strlen($request->remarks) > 100) {
            return $this->response->errorBadRequest('备注信息不可超过100字符');
        }

        // 处理化 收货地址、快递配送模板、自提门店
        $address = $freightTemplate = $store = null;
        if (! $test) {
            if (! $request->delivery_type) {
                return $this->response->errorBadRequest('请选择配送方式');
            }

            if ($request->delivery_type == Address::class) {
                if (! $request->delivery_id || ! $address = $this->user->addresses()->where('id', $request->delivery_id)->first()) {
                    return $this->response->errorBadRequest('请选择配送地址');
                }
                // 获取运费计算模板，没有则非配送范围
                if (! $freightTemplate = FreightTemplate::getTemplate($address)) {
                    return $this->response->errorBadRequest('当前地址不在配送范围，请重新选择地址');
                }
            } else {
                if (! $request->delivery_id || ! $store = Store::where('id', $request->delivery_id)->enabled()->first()) {
                    return $this->response->errorBadRequest('请选择自取门店');
                }
            }
        }

        if ($request->used_integral > 0) {
            $used_integral = intval($request->used_integral);
            if ($this->user->integral->balance < $used_integral) {
                return $this->response->errorBadRequest("当前积分剩余{$this->user->integral->blance}分，不够{$used_integral}分抵用");
            }
            $integral2money_rate = config('system.integral2money_rate', 0);
            if (empty($integral2money_rate) || $integral2money_rate < 0 || $integral2money_rate > 1) {
                return $this->response->errorBadRequest('当前积分无法使用，系统未设置正确的汇率');
            }
        } else {
            $used_integral = 0;
            $integral2money_rate = 0;
        }

        $goods_ids_list = $request->goods_list;
        if (blank($goods_ids_list) || ! is_array($goods_ids_list)) {
            return $this->response->errorBadRequest('下单商品不能为空');
        }
        $goods_specification_id2info = [];
        $inviter_ids = [];
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
            if (isset($goods['inviter_id']) && $goods['inviter_id'] > 0) {
                $inviter_ids[] = intval($goods['inviter_id']);
            }
        }

        $inviter_ids = array_unique($inviter_ids);

        // 排它锁 锁表 lockForUpdate
        $goods_specification_list = GoodsSpecification::with(['goods'])
            ->enabled()
            ->whereIn('id', array_keys($goods_specification_id2info))
            ->get();
        if ($goods_specification_list->count() !== count($goods_specification_id2info)) {
            return $this->response->errorBadRequest('存在商品规格不合法');
        }

        //验证优惠券
        if ($request->user_coupon_id) {
            $userCoupon = UserHasCoupon::find($request->user_coupon_id);
            if (empty($userCoupon) || !$userCoupon->isMine()) {
                return $this->response->errorBadRequest('优惠券不存在');
            }
            if ($userCoupon->status !== 1) {
                return $this->response->errorBadRequest('优惠券状态不可用');
            }
            // 先冻结
            if (! $userCoupon->freeze()) {
                return $this->response->errorBadRequest('优惠券冻结失败');
            }
        } else {
            $userCoupon = null;
        }

        $goods_specification_by_key_list = $goods_specification_list->keyBy('id');

        // 获取用户会员折扣
        $member_discount = \Auth::user()->getBestMemberDiscount(true);
        // 获取用户会员是否包邮
        $is_fee_freight = \Auth::user()->hasFeeFreight();

        $order_goods = [];
        $all_goods_price = 0;
        // 支持优惠券折扣的金额（已经是会员优惠后的价格）
        $allow_coupon_price = 0;
        // 所有商品会员优惠后的价格需要实付的金额（未使用优惠券）
        $all_member_discount_price = 0;
        $goods_count = 0;
        $goods_weight = 0;
        $sub_quantity = [];

        // 获取所有邀请人
        $inviters = User::getInviters($inviter_ids);
        $inviters = $inviters->keyBy('id');

        // 验证库存和促销合法性
        try {
            foreach ($goods_ids_list as $info) {
                $goods_id = intval($info['goods_id']);
                $specification_id = $info['goods_specification_id'];
                $inviter_id = $info['inviter_id'] ?? 0;

                $specification = $goods_specification_by_key_list[$specification_id];
                if ($specification->goods_id != $goods_id) {
                    // 回滚数据
                    throw new Exception('存在商品规格不合法');
                }
                $goods = $specification->goods;
                $goods_full_title = "【{$goods->title}（{$specification->title}）】";
                // 加入促销商品判断
                $marketing_type = $info['marketing_type'] ?? '';
                switch ($marketing_type) {
                    case DiscountGoods::class :
                        $marketing = DiscountGoods::find(($info['marketing_id'] ?? 0));
                        if (! ($marketing && $marketing->check($goods_id, $specification_id) && $marketing->hasEnabled())) {
                            // 不存在或者不匹配或者被禁用
                            throw new Exception("商品{$goods_full_title}促销活动不存在或已过期");
                        }

                        $goods_full_title = "【{$marketing->title}】";
                        // 促销原价卖（不参与会员优惠和优惠券优惠）
                        $discount_price = $goods_price = $marketing->price;

                        $model = $marketing;

                        $now_quantity = $marketing->quantity;

                        break;
                    default:
                        $marketing = null;
                        // 普通商品可会员价
                        $goods_price = $specification->price;
                        if ($member_discount < 100) {
                            $discount_price = ceil(strval($goods_price * $member_discount * 0.01));
                        } else {
                            $discount_price = $goods_price;
                        }

                        $model = $specification;

                        $now_quantity = $specification->quantity;

                        break;
                }

                if ($info['quantity'] > $now_quantity) {
                    // 存在商品库存不够，回滚
                    if ($now_quantity) {
                        throw new Exception("商品{$goods_full_title}库存紧剩{$now_quantity}件, 不足{$info['quantity']}件，请重新调整数量后下单");
                    } else {
                        throw new Exception("商品{$goods_full_title}已售罄");
                    }
                }

                //商品库存待删减
                $sub_quantity[] = [
                    'title' => $goods_full_title,
                    'model' => $model,
                    'quantity' => $info['quantity']
                ];

                // 计算总价（没有会员优惠的总价）
                $item_goods_all_price = $goods_price * $info['quantity'];
                // 已经会员优惠后的总价
                $item_goods_real_price = $discount_price * $info['quantity'];
                // 非活动商品，可加入计算优惠券
                if (! $marketing) {
                    $allow_coupon_price += $item_goods_real_price;
                }

                $order_goods_item = [
                    'goods_id' => $goods_id,
                    'goods_specification_id' => $specification_id,
                    // 快照记录
                    'snapshot' => $model->toSnapshot(),
                    'price' => $goods_price,
                    'quantity' => $info['quantity'],
                    'real_price' => $item_goods_real_price,
                    'inviter_id' => isset($inviters[$inviter_id]) ? $inviters[$inviter_id]->id : 0,
                ];
                if ($marketing) {
                    $order_goods_item['marketing_type'] = $marketing_type;
                    $order_goods_item['marketing_id'] = $marketing->id;
                }

                $order_goods[] = $order_goods_item;
                $all_member_discount_price += $item_goods_real_price;
                $all_goods_price += $item_goods_all_price;
                $goods_count += $info['quantity'];
                $goods_weight += $specification->weight * $info['quantity'];

            }

        } catch (\Exception $exception) {
            // 优惠券解冻
            if ($userCoupon && ! $userCoupon->unfreeze()) {
                return $this->response->errorBadRequest($exception->getMessage().'，优惠券解冻失败，请联系客服');
            }
            return $this->response->errorBadRequest($exception->getMessage());
        }

        $goods_weight = floatval(strval($goods_weight));
        $goods_count = intval(strval($goods_count));
        $all_goods_price = intval(strval($all_goods_price));
        $all_member_discount_price = intval(strval($all_member_discount_price));

        //优惠金额
        if ($userCoupon) {
            $coupon_price = $userCoupon->settleDiscount($allow_coupon_price);
            if ($coupon_price <= 0) {
                // 优惠券解冻
                if ($userCoupon && ! $userCoupon->unfreeze()) {
                    return $this->response->errorBadRequest('优惠券解冻失败，请联系客服');
                }
                return $this->response->errorBadRequest('当前优惠券无法享受优惠');
            }
        } else {
            $coupon_price = 0;
        }

        // 算运费的价格：会员折扣后使用优惠券的价格
        $no_freight_price = $all_member_discount_price - $coupon_price;

        // 获取用户会员是否包邮
        if ($is_fee_freight) {
            $freight_price = 0;
        } else {
            // 根据地区计算配送费和运费模板
            // 测试模式可能没有address
            if ($freightTemplate) {
                $freight_price = $freightTemplate->compute($goods_weight, $goods_count, $no_freight_price);
            } else {
                $freight_price = 0;
            }
        }

        // 总费用（商品原价+快递费）
        $all_price = $all_goods_price + $freight_price;
        // 积分抵扣的金额
        if ($used_integral) $integral_price = to_int($used_integral * $integral2money_rate * 100);
        else $integral_price = 0;
        // 支付费用（优惠后的会员价-优惠券折扣+快递费-积分抵扣）
        $real_price = $no_freight_price + $freight_price - $integral_price;
        if ($real_price <= 0) {
            return $this->response->errorBadRequest('积分抵扣金额部分不能超过总价');
        }

        $order = new Order();
        $order->order_number = Order::generateOrderNumber();
        $order->order_type = 1;//普通下单
        $order->user_id = $this->user->id;
        $order->goods_price = $all_goods_price;
        $order->member_discount_price = $all_member_discount_price;
        $order->member_discount = $member_discount;
        $order->freight_price = $freight_price;
        $order->all_price = $all_price;
        $order->coupon_price = $coupon_price;
        $order->allow_coupon_price = $allow_coupon_price;
        $order->user_coupon_id = $userCoupon ? $userCoupon->id : 0;
        $order->used_integral = $used_integral;
        $order->integral_rate = $integral2money_rate;
        $order->integral_price = $integral_price;
        $order->real_price = $real_price;
        $order->goods_count = $goods_count;
        $order->goods_weight = $goods_weight;
        $order->status = 1;
        $order->remarks = $request->remarks ?: '';
        $order->delivery_type = $request->delivery_type;
        $order->delivery_id = $request->delivery_id;
        $order->delivery_snapshot = $request->delivery_type == Address::class ? ($address ? $address->toArray() : []) : ($store ? $store->toArray() : []);
//        $order->address_id = $request->address_id;
//        $order->address_snapshot = $address ? $address->toSnapshot() : [];
        // 运费模板
        $order->freight_template_id = ($is_fee_freight || empty($freightTemplate)) ? 0 : $freightTemplate->id;

        DB::beginTransaction();

        try {
            // 减库存
            foreach ($sub_quantity as $item) {
                $res = $item['model']->sold($item['quantity']);
                if ($res === false) {
                    DB::rollBack();
                    // 优惠券解冻
                    if ($userCoupon && ! $userCoupon->unfreeze()) {
                        return $this->response->errorBadRequest('优惠券解冻失败，请联系客服');
                    }
                    $specification = GoodsSpecification::find($item['model']->id);
                    if ($specification->quantity) {
                        return $this->response->errorBadRequest("商品{$item['title']}库存紧剩{$specification->quantity}件, 不足{$item['quantity']}件，请重新调整数量后下单");
                    } else {
                        return $this->response->errorBadRequest("商品{$item['title']}已售罄");
                    }
                }
            }

            $order->save();

            $order->orderGoods()->createMany($order_goods);

            // 测试计算
            if ($test === 1) {
                DB::rollBack();
                // 优惠券解冻
                if ($userCoupon && ! $userCoupon->unfreeze()) {
                    return $this->response->errorBadRequest('优惠券解冻失败，请联系客服');
                }
            } else {
                // 清购物车
                if ($from_shopping_cart_ids) {
                    ShoppingCart::isMine()->whereIn('id', $from_shopping_cart_ids)->delete();
                }

                // 修改优惠券为已使用
                if ($userCoupon && ! $userCoupon->useIt($coupon_price)) {
                    DB::rollBack();
                    return $this->response->errorBadRequest('优惠券状态异常，结算失败');
                }
                // 扣除积分
                if ($used_integral) {
                    try {
                        $this->user->integral->useIt($order, 0);
                    } catch (\Exception $exception) {
                        DB::rollBack();
                        return $this->response->errorBadRequest($exception->getMessage());
                    }


                }

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

    public function show(Order $order)
    {
        // 职员可以查询
        if (! $order->isMine() && ! $this->user->isStaff()) return $this->response->errorForbidden();
//        $this->authorize('is-mine', $order);
        return $this->response->item($order, new OrderTransformer());
    }

    // 获取物流信息，仅支持3已发货待收货，4已收货交易完成
    public function logistics(Order $order)
    {
        $this->authorize('is-mine', $order);

        if (! in_array($order->status, [3, 4])) {
            return $this->response->errorBadRequest('订单状态无法查询');
        }

        try {
            $res = $order->getLogistics();
            return $this->response->array($res);
        } catch (\Exception $exception) {
            return $this->response->errorBadRequest($exception->getMessage());
        }
    }

    /**
     * 取消订单
     * @param Order $order
     * @author klinson <klinson@163.com>
     * @return \Dingo\Api\Http\Response|void
     */
    public function cancel(Order $order, Request $request)
    {
        $this->authorize('is-mine', $order);

        if (! in_array($order->status, [1, 2])) {
            return $this->response->errorBadRequest('订单状态不可取消');
        }
        if (! $request->reason) {
            return $this->response->errorBadRequest('请选择取消原因');
        }

        try {
            $order->cancel($request->reason);

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
        $balance = to_int($request->balance);
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
                    $this->user->wallet->save();
                    $this->user->wallet->log($balance, $order, "支付订单（{$order->order_number}）");
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
        else {
            if (config('app.env') !== 'local') {
                $order->real_cost = $order->real_price;
                $order->used_balance = 0;

                $app = app('wechat.payment');
                $config = $app->getConfig();
                $order_title = "商城订单：{$order->order_number}";
                if (app()->environment() !== 'production') $order_title = '【测试】'.$order_title;
                $result = $app->order->unify([
                    'body' => $order_title,
                    'out_trade_no' => $order->order_number,
                    'total_fee' => $order->real_cost,
                    'trade_type' => 'JSAPI',
                    'spbill_create_ip' => $request->getClientIp(),
                    'openid' => \Auth::user()->wxapp_openid,
                    'notify_url' => Order::getWechatPayNotifyUrl(),
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
    }

    // 确认收到货
    public function receive(Order $order, Request $request)
    {
        if (! (
            ($order->delivery_type == Address::class && $order->isMine())
            || $order->delivery_type == Store::class && $this->user->isStaff()
        )) return $this->response->errorForbidden();
//        $this->authorize('is-mine', $order);
        if (! $order->receive()) {
            return $this->response->errorBadRequest('订单无法确认，请查看订单状态');
        }

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