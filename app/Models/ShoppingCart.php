<?php

namespace App\Models;

class ShoppingCart extends Model
{
    protected $fillable = ['goods_id', 'quantity', 'goods_specification_id', 'inviter_id', 'marketing_type', 'marketing_id'];

    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id')->withDefault(['title' => '[已删除]']);
    }

    public function specification()
    {
        return $this->hasOne(GoodsSpecification::class, 'id', 'goods_specification_id')->withDefault(['title' => '[已删除]', 'price' => 0, 'quantity' => 0]);
    }
}
