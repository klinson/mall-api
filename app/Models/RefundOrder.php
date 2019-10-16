<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundOrder extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'order_id', 'order_goods_id', 'order_number', 'user_id', 'goods_id', 'goods_specification_id', 'reason_text', 'reason_images', 'quantity', 'price', 'real_price', 'real_refund_cost', 'real_refund_balance', 'freight_price', 'status', 'reject_reason', 'refund_order_number', 'express_id', 'express_number', 'expressed_at', 'confirmed_at'
    ];

    //状态，0已撤销，1申请中，2通过待买家发货，3已发货待卖家确认到货，4已退款，5确认到货拒绝退款，6申请后直接拒绝
    const status_text = [
        '已撤销', '待审核', '待买家发货', '待确认到货', '已退款', '拒绝退款', '驳回审核'
    ];

    protected static function boot()
    {
        self::saved(function ($model) {
             OrderGoods::where('id', $model->order_goods_id)->update(['refund_status' => $model->status]);
        });
        parent::boot();
    }

    protected $casts = [
        'reason_images' => 'array'
    ];

    public static function generateOrderNumber()
    {
        return date('YmdHis') . random_string(11);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderGoods()
    {
        return $this->belongsTo(OrderGoods::class);
    }

    // 执行退款逻辑
    public function refund()
    {
        if ($this->order->used_balance) {
            // 退余额
            $this->order->user->wallet->increment('balance', $this->real_price);
            $this->order->user->wallet->save();
            $this->order->user->wallet->log($this->real_price, $this, "订单（{$this->order->order_number}）的售后订单（{$this->order_number}）退款退回余额");
        } else {
            // 退微信

        }
    }
}
