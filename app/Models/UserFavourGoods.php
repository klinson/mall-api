<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFavourGoods extends Model
{
    use HasOwnerHelper;

    public $timestamps = false;

    const goods_types = [
        Goods::class,
        DiscountGoods::class
    ];

    protected $fillable = [
        'user_id', 'goods_id', 'goods_type', 'created_at'
    ];

    public function favourGoods()
    {
        return $this->morphTo('favourGoods', 'goods_type', 'goods_id');
    }

}
