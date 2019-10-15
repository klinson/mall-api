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
}
