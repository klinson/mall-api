<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use function EasyWeChat\Kernel\Support\get_client_ip;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Exception;
use Log;

class RechargeThresholdOrder extends Model
{
    use SoftDeletes, HasOwnerHelper;

    const wechat_pay_notify_route = 'RechargeThresholdOrder.wechat.pay.notify';

    protected $fillable = ['order_number', 'balance', 'user_id', 'agency_config_id', 'status'];

    public static function getWechatPayNotifyUrl()
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route(self::wechat_pay_notify_route);
    }

    public static function generateOrder($user, AgencyConfig $agencyConfig)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }
        $data = [
            'balance' => $agencyConfig->id,
            'user_id' => $user_id,
            'agency_config_id' => $agencyConfig->id,
            'status' => 1,
        ];
        $order = new self($data);
        $order->order_number = self::generateOrderNumber();
        $order->save();
        return $order;
    }

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    public function generatePayConfig()
    {
        $app = app('wechat.payment');
        $config = $app->getConfig();
        $order_title = "【".config('app.name')."】代理充值订单：{$this->order_number}";
        $result = $app->order->unify([
            'body' => $order_title,
            'out_trade_no' => $this->order_number,
            'total_fee' => $this->balance,
            'trade_type' => 'JSAPI',
            'spbill_create_ip' => get_client_ip(),
            'openid' => $this->owner->wxapp_openid,
            'notify_url' => self::getWechatPayNotifyUrl(),
        ]);

//        dd($result);
        Log::info("[wechat][payment][pay][{$this->order_number}]微信支付下单返回：" . json_encode($result, JSON_UNESCAPED_UNICODE));
        if (($result['return_code'] ?? 'FAIL') == 'FAIL') {
            Log::error("[wechat][payment][pay][{$this->order_number}]微信支付下单失败：[{$result['return_code']}]{$result['return_msg']}");
            throw new Exception('支付失败，' . $result['return_msg']);
        }

        if (($result['result_code'] ?? 'FAIL') == 'FAIL') {
            Log::error("[wechat][payment][pay][{$this->order_number}]微信支付下单失败：[{$result['err_code']}]{$result['err_code_des']}");
            throw new Exception('支付失败，' . $result['err_code_des']);

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
            'order_number' => $this->order_number,
            'order_title' => $order_title,
            'order_price' => $this->real_cost,
        ];

        return $return;
    }

    public function agencyConfig()
    {
        return $this->belongsTo(AgencyConfig::class);
    }

    // 设置支付成功
    public function setSuccess()
    {
        DB::beginTransaction();

        try {
            // 修改订单状态
            $this->status = 2;
            $this->payed_at = date('Y-m-d H:i:s');
            $this->save();

            // 修改用户代理等级
            $this->owner->agency_id = $this->agency_config_id;
            $this->owner->save();

            // 充值入账
            $this->owner->wallet->increment('balance', $this->balance);
            $this->owner->wallet->save();
            $this->owner->wallet->log($this->balance, $this, "充值'{$this->agencyConfig->title}'代理（{$this->order_number}）", 1);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}

