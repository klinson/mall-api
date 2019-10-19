<?php

namespace App\Models;

class OrderGoods extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'goods_id', 'goods_specification_id', 'goods_info', 'price', 'quantity', 'snapshot', 'inviter_id',
    ];

    protected $casts = [
        'snapshot' => 'array'
    ];

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id')->withDefault(['title' => '[已删除]']);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function specification()
    {
        return $this->hasOne(GoodsSpecification::class, 'id', 'goods_specification_id');
    }

    public function refundOrder()
    {
        return $this->hasOne(RefundOrder::class, 'order_goods_id', 'id')->orderBy('id', 'desc');
    }
}
