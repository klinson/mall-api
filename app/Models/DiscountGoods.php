<?php

namespace App\Models;

class DiscountGoods extends Model
{
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}
