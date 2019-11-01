<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use App\Transformers\MemberLevelTransformer;
use App\Transformers\MemberRechargeActivityTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use function EasyWeChat\Kernel\Support\get_client_ip;
use \Exception;
use DB;
use Log;

class MemberRechargeOrder extends Model
{
    use SoftDeletes, HasOwnerHelper;

    const wechat_pay_notify_route = '/api/wechat/MemberRechargeOrderPaidNotify';

    protected $casts = [
        'member_level_snapshot' => 'array',
        'member_recharge_activity_snapshot' => 'array'
    ];

    protected $fillable = [
        'order_number', 'user_id', 'balance', 'member_recharge_activity_id', 'member_recharge_activity_snapshot', 'member_level_id', 'member_level_snapshot', 'status', 'inviter_id'
    ];

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    public static function getWechatPayNotifyUrl()
    {
        return config('app.url').self::wechat_pay_notify_route;
    }

    public static function generateOrder($user, MemberRechargeActivity $activity, $inviter)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }
        $inviter_id = 0;
        if ($inviter) {
            if ($inviter instanceof User) {
                $inviter_id = $inviter->id;
            } else {
                $inviter_id = intval($inviter);
            }
        }
        $data = [
            'order_number' => self::generateOrderNumber(),
            'user_id' => $user_id,
            'balance' => $activity->recharge_threshold,
            'member_recharge_activity_id' => $activity->id,
            'member_recharge_activity_snapshot' => (new MemberRechargeActivityTransformer())->transform($activity),
            'member_level_id' => $activity->memberLevel->id,
            'member_level_snapshot' => (new MemberLevelTransformer())->transform($activity->memberLevel),
            'status' => 1,
            'inviter_id' => $inviter_id
        ];
        $order = new self($data);
        $order->save();

        return $order;
    }

    public function generatePayConfig()
    {
        $app = app('wechat.payment');
        $config = $app->getConfig();
        $order_title = "【".config('app.name')."】会员充值活动订单：{$this->order_number}";
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
            'order_price' => $this->balance,
        ];

        return $return;
    }

    public function generateValidityDate()
    {
        $this->validity_started_at = $this->payed_at;
        $activity = $this->member_recharge_activity_snapshot;
        if ($activity['validity_type'] == 4) {
            $this->validity_ended_at = null;
        } else {
            $method = MemberRechargeActivity::VALIDITY_TYPE2METHODS[$activity['validity_type']];
            $this->validity_ended_at = Carbon::createFromTimestamp(strtotime($this->validity_started_at))->$method($activity['validity_times'])->toDateTimeString();
        }
    }

    // 设置支付成功
    public function setSuccess()
    {
        DB::beginTransaction();

        try {
            // 修改订单状态
            $this->status = 2;
            $this->payed_at = date('Y-m-d H:i:s');
            $this->generateValidityDate();
            $this->save();

            $activity = $this->member_recharge_activity_snapshot;
            //记录用户会员等级和有效期
            UserHasMemberLevel::generate($this);
            $log_info = "{$activity['title']}订单（{$this->order_number}）入账";
            $this->owner->memberInit();

            // 充值入账
            $this->owner->wallet->increment('balance', $this->balance);
            $this->owner->wallet->save();
            $this->owner->wallet->log($this->balance, $this, $log_info, 1);


            DB::commit();
            // 发起邀请结算
            if ($this->inviter_id) {

            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
