<?php

namespace App\Models;

use App\Jobs\SettleMemberRechargeOrderJob;
use App\Models\Traits\HasOwnerHelper;
use App\Transformers\MemberLevelTransformer;
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

    // 1待支付，2已支付，3已过期
    const status_text = [
        1 => '待支付',
        2 => '已支付',
        3 => '已过期'
    ];

    protected $casts = [
        'member_level_snapshot' => 'array',
        'member_recharge_activity_snapshot' => 'array'
    ];

    protected $fillable = [
        'order_number', 'user_id', 'balance', 'member_recharge_activity_id', 'member_recharge_activity_snapshot', 'member_level_id', 'member_level_snapshot', 'status', 'inviter_id'
    ];

    public function scopeHasPayed($query)
    {
        return $query->where('status', 2);
    }

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
            'member_recharge_activity_snapshot' => $activity->toSnapshot(),
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

    public function generateValidityDate($start_date = null)
    {
        if ($start_date) {
            $this->validity_started_at = $start_date;
        } else {
            $this->validity_started_at = $this->payed_at;
        }
        $activity = $this->member_recharge_activity_snapshot;
        if ($activity['validity_type'] == 4) {
            $this->validity_ended_at = null;
        } else {
            $method = MemberRechargeActivity::VALIDITY_TYPE2METHODS[$activity['validity_type']];
            $this->validity_ended_at = Carbon::createFromTimestamp(strtotime($this->validity_started_at))->$method($activity['validity_times'])->toDateTimeString();
        }
    }

    public function scopeHasPaid($query)
    {
        return $query->where('status', 2);
    }

    // 设置支付成功
    public function setSuccess()
    {
        DB::beginTransaction();

        try {
            // 修改订单状态
            $this->status = 2;
            $this->payed_at = date('Y-m-d H:i:s');

            $last_member_level = $this->owner->getLastMemberLevel($this->member_level_id);

            if ($last_member_level && $last_member_level->validity_ended_at && $last_member_level->validity_ended_at > $this->payed_at) {
                $this->generateValidityDate($last_member_level->validity_ended_at);
            } else {
                $this->generateValidityDate();
            }

            $this->save();

            $activity = $this->member_recharge_activity_snapshot;
            //记录用户会员等级和有效期
            UserHasMemberLevel::generate($this);
            $this->owner->memberInit();

            // 优惠券入账
            if (! empty($activity['coupons']['data'])) {
                $log_info = "{$activity['title']}订单（{$this->order_number}）入账";

                $coupon_ids = collect($activity['coupons']['data'])->pluck('id')->toArray();

                $coupons = Coupon::whereIn('id', $coupon_ids)->get()->keyBy('id');
                $userCoupons = collect();
                foreach ($activity['coupons']['data'] as $datum) {
                    if ($datum['count'] > 0) {
                        $tmp = $coupons[$datum['id']]->toUser($this->owner, $log_info, $datum['count']);
                        if ($tmp->isNotEmpty()) {
                            $userCoupons = $userCoupons->concat($tmp);
                        }
                    }
                }

                // 记录到订单
                $this->recordUserCoupons($userCoupons);
            }

            // 充值入账，已取消，已改成优惠券赠送
//            $this->owner->wallet->increment('balance', $this->balance);
//            $this->owner->wallet->save();
//            $this->owner->wallet->log($this->balance, $this, $log_info, 1);


            DB::commit();
            // 发起邀请结算
            if ($this->inviter_id && $this->inviter) {
                dispatch(new SettleMemberRechargeOrderJob($this));
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function recordUserCoupons($userCoupons)
    {
        if ($userCoupons->isEmpty()) {
            return false;
        }
        $user_coupon_ids = $userCoupons->pluck('id')->toArray();
        $this->coupons()->sync($user_coupon_ids);
    }

    public function coupons()
    {
        return $this->belongsToMany(UserHasCoupon::class, 'member_recharge_order_has_coupons', 'order_id', 'user_coupon_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function settle()
    {
        if ($this->inviter) {
            $this->inviter->coffer->settleMemberRechargeOrder($this->invite_real_award, $this);
        }
    }

    // 实际佣金
    public function getInviteRealAwardAttribute()
    {
        return $this->member_recharge_activity_snapshot['invite_real_award'];
    }

    public function memberRechargeActivity()
    {
        return $this->belongsTo(MemberRechargeActivity::class);
    }

    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class);
    }
}
