<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    // 1待支付，2已支付待发货，3已发货待收货，4已收货交易完成，5取消订单，6超时过期
    const status_text = [
        '未知', '待支付', '待发货', '待收货', '已完成', '已订单'
    ];

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
    public function express()
    {
        $this->status = 3;
        $this->save();
        // TODO: 日志记录
        // TODO: 通知
    }
}
