<?php

namespace App\Models;

use App\Jobs\AutoReceiveOrderJob;
use App\Jobs\UnsettleOrderJob;
use App\Models\Traits\HasOwnerHelper;
use App\Models\Traits\ScopeDateHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Log;

class Order extends Model
{
    use SoftDeletes, ScopeDateHelper, HasOwnerHelper;

    protected $casts = [
        'address_snapshot' => 'array'
    ];

    const wechat_pay_notify_route = '/api/wechat/OrderPaidNotify';

    // 1待支付，2已支付待发货，3已发货待收货，4已收货交易完成，5取消订单，6超时过期，7退款
    const status_text = [
        '未知', '待支付', '待发货', '待收货', '已完成', '已取消', '超时过期', '已退款'
    ];

    const express_status_text = [
        0 => '在途', 1 => '揽收', 2 => '疑难', 3 => '签收', 4 => '退签', 5 => '派件', 6 => '退回', -1 => '未知'
    ];

    public static function getWechatPayNotifyUrl()
    {
        return config('app.url').self::wechat_pay_notify_route;
    }

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    public function orderGoods()
    {
        return $this->hasMany(OrderGoods::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(['nickname' => '[已删除]', 'mobile' => '']);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id')->withDefault(['address' => '[已删除]', 'mobile' => '']);
    }

    /**
     * 获取快递100物流信息
     * @author klinson <klinson@163.com>
     * @return mixed
     */
    public function getLogistics()
    {
        if (empty($this->express_id) || empty($this->express_number)) {
            throw new \Exception('该订单未设置物流单号');
        }

        $config = config('services.kuaidi100');
        $express = new \Puzzle9\Kuaidi100\Express($config['key'], $config['customer'], $config['callbackurl']);

        if ($this->express->code === 'shunfeng') {
            $rev_phone = $this->address->mobile;
        } else {
            $rev_phone = null;
        }

        //实时查询 https://www.kuaidi100.com/openapi/api_post.shtml
        $res = $express->synquery($this->express->code, $this->express_number, $rev_phone); // 快递服务商 快递单号 手机号

        if (isset($res['status']) && $res['status'] == 200) {
            $res['com_name'] = Express::getNameByCode($res['com']);
            $express_status = intval($res['state']);
            if ($express_status !== $this->express_status) {
                $this->express_status = $express_status;
                $this->save();
            }
            return $res;
        } else if (isset($res['result']) && $res['result'] == false) {
            throw new \Exception($res['message']);
        } else {
            throw new \Exception('获取物流失败');
        }
    }

    public function express()
    {
        return $this->belongsTo(Express::class, 'express_id', 'id')->withDefault([
            'id' => 0,
            'name' => '无'
        ]);
    }

    public function specifications()
    {
        return $this->belongsToMany(GoodsSpecification::class, 'order_goods', 'order_id', 'goods_specification_id', 'id', 'id')->withPivot(['quantity']);
    }

    public function resetSpecificationQuantity()
    {
        foreach ($this->specifications as $specification) {
            $specification->sold($specification->pivot_quantity, true);
        }
    }

    // 取消订单
    public function cancel()
    {
        if (! in_array($this->status, [1, 2, 3, 4])) {
            throw new \Exception('订单状态无法取消');
        }

        if ($this->status == 1) {
            try {
                \DB::beginTransaction();

                $this->status = 5;
                $this->save();

                // 恢复库存
                $this->resetSpecificationQuantity();

                \DB::commit();
                return $this;
            } catch (\Exception $exception) {
                DB::rollBack();
                throw new \Exception('退款失败');
            }
        }

        try {
            \DB::beginTransaction();

            if ($this->used_balance > 0) {
                $this->user->wallet->increment('balance', $this->used_balance);
                $this->user->wallet->save();
                $this->user->wallet->log($this->used_balance, $this, "取消订单（{$this->order_number}）退款入账", 1);
            } else {
                $this->cancel_order_number = self::generateOrderNumber();
                if (! app()->isLocal()) {
                    $app = app('wechat.payment');
                    $result = $app->refund->byOutTradeNumber($this->order_number, $this->cancel_order_number, $this->real_cost, $this->real_cost, [
                        // 可在此处传入其他参数，详细参数见微信支付文档
                        'refund_desc' => "订单【{$this->order_number}】取消自动退款",
                    ]);
                    Log::info("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款：" . json_encode($result, JSON_UNESCAPED_UNICODE));

                    if (($result['return_code'] ?? 'FAIL') == 'FAIL') {
                        Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款失败：[{$result['return_code']}]{$result['return_msg']}");
                        throw new \Exception('退款失败，' . $result['return_msg'], 200);
                    }
                    if (($result['result_code'] ?? 'FAIL') == 'FAIL') {
                        Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款失败：[{$result['err_code']}]{$result['err_code_des']}");
                        throw new \Exception('退款失败，' . $result['err_code_des'], 200);
                    }
                    Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付退款成功：{$this->real_cost}");
                } else {
                    // 本地直接成功
                    Log::error("[wechat][payment][refund][{$this->order_number}][{$this->cancel_order_number}]微信支付local环境退款成功：{$this->real_cost}");
                }
            }

            $this->status = 5;
            $this->save();

            \DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            if ($exception->getCode() != 200) {
                Log::error("[order][refund][error][{$this->order_number}]订单取消异常，{$exception->getMessage()}，{$exception->getCode()}，{$exception->getFile()}，{$exception->getLine()}");

                throw new \Exception('退款失败');
            } else {
                throw new \Exception($exception->getMessage());
            }
        }
    }

    // 发货
    public function expressing($express_number = null, $express_id = 0)
    {
        if (empty($express_id)) {
            $express_id = config('system.express_company_id', 0);
        }
        $this->status = 3;
        $this->expressed_at = date('Y-m-d H:i:s');
        $this->express_id = $express_id;
        $this->express_number = $express_number ?: '';

        $this->save();

        // 定时N天去确认到货
        dispatch(new AutoReceiveOrderJob($this->id))->delay(Carbon::now()->addDays(config('system.order_auto_receive_days', 7)));
    }

    /**
     * 支付
     * @param int $used_balance
     * @author klinson <klinson@163.com>
     */
    public function pay($used_balance = 0)
    {
        // 直接支付成功
        $this->used_balance = $used_balance;
        if ($used_balance >= $this->real_price) {
            $this->real_cost = 0;
        } else {
            $this->real_cost = $this->real_price - $used_balance;
        }
        $this->status = 2;
        $this->payed_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function receive()
    {
        $this->status = 4;
        $this->confirmed_at = date('Y-m-d H:i:s');
        $this->save();

//        dispatch(new UnsettleOrderJob($this));
    }

    /**
     * 订单状态统计
     * @param $user
     * @author klinson <klinson@163.com>
     * @return array
     */
    public static function statusCount($user)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }

        $res = DB::table('orders')
            ->whereNull('deleted_at')
            ->where('user_id', $user_id)
            ->groupBy('status')
            ->select(['status', \DB::raw('count(*) as total_count')])
            ->orderBy('status')
            ->get();

        $return = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
        ];

        if ($res->isEmpty()) {
            return $return;
        }

        foreach ($res as $re) {
            $return[$re->status] = $re->total_count;
            $return[0] += $re->total_count;
        }

        return $return;
    }

    public function refunds()
    {
        return $this->hasMany(RefundOrder::class, 'order_id', 'id');
    }

    // 待结算记录
    public function unsettle()
    {
        // 自己是代理直接结算给自己，自己不是代理则按邀请购买人进行结算
        if ($this->owner->isAgency()) {
            // 计算出待结算金额
            $balance = $this->owner->agency->unsettleOrder($this);

            $this->owner->coffer->unsettle($balance, $this, $this->owner->agency, 1);
        } else {
            $orderGoods = $this->orderGoods()->with(['inviter'])->whereHas('inviter')->get();
            foreach ($orderGoods as $orderGood) {
                // 邀请人是代理才进行结算
                if (! $orderGood->inviter) continue;
                if (! $orderGood->inviter->isAgency()) continue;

                // 计算出待结算金额
                $balance = $orderGood->inviter->agency->unsettleOrderGoods($orderGood);

                // 待结算记录
                $orderGood->inviter->coffer->unsettle($balance, $this, $orderGood->inviter->agency, 1);
            }
        }
    }
}
