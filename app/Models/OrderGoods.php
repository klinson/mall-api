<?php

namespace App\Models;

class OrderGoods extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'goods_id', 'goods_specification_id', 'goods_info', 'price', 'quantity', 'snapshot', 'inviter_id', 'refund_status'
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

    public function toString()
    {
        $status = RefundOrder::status_text[$this->refund_status];
        return "{$this->snapshot['goods']['title']}-{$this->snapshot['title']}（{$this->price}）X {$this->quantity} 【{$status}】";
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
