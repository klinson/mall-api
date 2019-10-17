<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Order extends Model
{
    use SoftDeletes;

    const wechat_pay_notify_route = 'order.wechat.pay.notify';

    // 1待支付，2已支付待发货，3已发货待收货，4已收货交易完成，5取消订单，6超时过期，7退款
    const status_text = [
        '未知', '待支付', '待发货', '待收货', '已完成', '已取消', '超时过期', '已退款'
    ];

    public static function getWechatPayNotifyUrl()
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route(self::wechat_pay_notify_route);
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
        $config = config('services.kuaidi100');
        $express = new \Puzzle9\Kuaidi100\Express($config['key'], $config['customer'], $config['callbackurl']);

        //实时查询
        $list = $express->synquery($this->express->code, $this->express_number, $this->address->mobile); // 快递服务商 快递单号 手机号
        return $list;
    }

    public function express()
    {
        return $this->belongsTo(Express::class, 'express_id', 'id');
    }

    // 取消订单
    public function cancel()
    {
        $status = $this->status;
        $this->status = 5;
        try {
            \DB::beginTransaction();
            if ($status > 1) {
                // TODO: 退款处理
            }
            $this->save();
            \DB::commit();

            // TODO: 日志记录
            // TODO: 通知
        } catch (\Exception $exception) {

            DB::rollBack();
        }
    }

    // 发货
    public function expressing($express_number = null)
    {
        $this->status = 3;
        $this->expressed_at = date('Y-m-d H:i:s');
        $this->express_id = config('system.express_company_id', 0);
        $this->express_number = $express_number ?: '';

        $this->save();
        // TODO: 日志记录
        // TODO: 通知
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
}
