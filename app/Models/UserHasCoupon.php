<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserHasCoupon extends Model
{
    use SoftDeletes, HasOwnerHelper;

    protected $fillable = [
        'user_id', 'coupon_snapshot', 'coupon_id', 'status', 'discount_money', 'has_enabled', 'description'
    ];

    protected $casts = [
        'coupon_snapshot' => 'array'
    ];
}
