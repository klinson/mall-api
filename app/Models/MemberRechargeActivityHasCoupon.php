<?php

namespace App\Models;

class MemberRechargeActivityHasCoupon extends Model
{
    protected $primaryKey = 'coupon_id';

    public $timestamps = false;

    protected $fillable = ['coupon_id', 'activity_id', 'count'];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}
