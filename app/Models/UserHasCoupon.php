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

    public function getFaceValueAttribute()
    {
        return $this->coupon_snapshot['face_value'];
    }

    public function getStartPriceAttribute()
    {
        return $this->coupon_snapshot['start_price'];
    }

    public function getTypeAttribute()
    {
        return $this->coupon_snapshot['type'];
    }
}
