<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfflineOrder extends Model
{
    use SoftDeletes, HasOwnerHelper;

    const wechat_pay_notify_route = '/api/wechat/OfflineOrderPaidNotify';

    const status_text = [
        '未下单', '待确认', '待支付', '已完成', '已取消'
    ];

    protected $fillable = ['store_id', 'staff_id', 'all_price', 'real_price', 'staff_id', 'status', 'order_number'];

    public static function getWechatPayNotifyUrl()
    {
        return config('app.url').self::wechat_pay_notify_route;
    }

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
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
        $this->status = 3;
        $this->payed_at = date('Y-m-d H:i:s');
        $this->save();
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
