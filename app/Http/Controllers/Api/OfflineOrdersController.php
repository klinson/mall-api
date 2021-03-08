<?php
/**
 * Created by PhpStorm.
 * User: klinson <klinson@163.com>
 * Date: 2021/3/2
 * Time: 00:20
 */

namespace App\Http\Controllers\Api;


use App\Models\OfflineOrder;
use App\Transformers\OfflineOrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OfflineOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = OfflineOrder::query();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->order_number) {
            $query->where('order_number', "like", "%{$request->order_number}%");
        }
        if ($request->show_staff) {
            $this->authorize('is-staff');
            $query->where('staff_id', $this->user->id);
        } else {
            $query->isMine();
        }

        $list = $query->recent()->paginate($request->per_page);
        return $this->response->paginator($list, new OfflineOrderTransformer());
    }

    public function show(OfflineOrder $order)
    {
        if (! ($order->user_id === 0 || $order->user_id == $this->user->id || ($this->user->isStaff() && $order->staff_id == $this->user->id))) return $this->response->errorForbidden();

        return $this->response->item($order, new OfflineOrderTransformer());
    }

    // 员工下单
    public function store(Request $request)
    {
        $this->authorize('is-staff');

        $this->validate($request, [
            'store_id' => 'required',
            'all_price' => 'required|integer|min:1'
        ], [], [
            'store_id' => '门店',
            'all_price' => '订单金额'
        ]);

        $data = $request->only(['store_id', 'all_price']);
        $data['staff_id'] = $this->user->id;
        $data['status'] = 1;
        $data['real_price'] = $data['all_price'];
        $data['order_number'] = OfflineOrder::generateOrderNumber();

        $order = OfflineOrder::create($data);
        return $this->response->item($order, new OfflineOrderTransformer());
    }

    // 买家确认订单
    public function confirm(Request $request, OfflineOrder $order)
    {
        if (! in_array($order->status, [1, 2])) return $this->response->errorBadRequest('订单状态异常');
        if ($order->staff_id == $this->user->id) return $this->response->errorBadRequest('不能自己给自己的下单');

        $used_integral = intval($request->used_integral ?: 0);

        DB::beginTransaction();

        // 之前下过单，要退回积分
        if ($order->status == 2 && $order->used_integral) {
            try {
                $this->user->integral->useIt($order, 2);
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->response->errorBadRequest($exception->getMessage());
            }
        }

        if ($used_integral) {
            $integral2money_rate = config('system.integral2money_rate', 0);
            if (empty($integral2money_rate) || $integral2money_rate < 0 || $integral2money_rate > 1) {
                return $this->response->errorBadRequest('当前积分无法使用，系统未设置正确的汇率');
            }

            if ($this->user->integral->balance < $used_integral) {
                return $this->response->errorBadRequest("当前积分剩余{$this->user->integral->blance}分，不够{$used_integral}分抵用");
            }
            $order->used_integral = $used_integral;

            try {
                $this->user->integral->useIt($order, 0);
            } catch (\Exception $exception) {
                DB::rollBack();
                return $this->response->errorBadRequest($exception->getMessage());
            }

            // 积分抵扣的金额
            $integral_price = to_int($used_integral * $integral2money_rate * 100);
            $order->real_price = $order->all_price - $integral_price;
            $order->integral_price = $integral_price;
            $order->integral_rate = $integral2money_rate;
            if ($order->real_price <= 0) {
                DB::rollBack();
                return $this->response->errorBadRequest('积分抵扣金额部分不能超过总价');
            }
        } else {
            $order->real_price = $order->all_price;
            $order->integral_price = 0;
            $order->integral_rate = 0;
            $order->used_integral = 0;
        }
        $order->remarks = $request->remarks;
        $order->confirmed_at = date('Y-m-d H:i:s');
        $order->status = 2;
        $order->user_id = $this->user->id;
        $order->save();

        DB::commit();

        return $this->response->item($order, new OfflineOrderTransformer());
    }

    // 支付
    public function pay(Request $request, OfflineOrder $order)
    {
        $this->authorize('is-mine', $order);

        if ($order->status != 2) return $this->response->errorBadRequest('订单状态异常');

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
                    $this->user->wallet->log($balance, $order, "支付线下订单（{$order->order_number}）");
                    $order->pay($balance);
                    DB::commit();
                    return $this->response->item($order, new OfflineOrderTransformer());
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
                $order_title = "线下订单：{$order->order_number}";
                if (app()->environment() !== 'production') $order_title = '【测试】'.$order_title;
                $result = $app->order->unify([
                    'body' => $order_title,
                    'out_trade_no' => $order->order_number,
                    'total_fee' => $order->real_cost,
                    'trade_type' => 'JSAPI',
                    'spbill_create_ip' => $request->getClientIp(),
                    'openid' => \Auth::user()->wxapp_openid,
                    'notify_url' => OfflineOrder::getWechatPayNotifyUrl(),
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
                return $this->response->item($order, new OfflineOrderTransformer());
            }
        }

    }


}
