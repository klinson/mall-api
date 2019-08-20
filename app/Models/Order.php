<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

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
}
