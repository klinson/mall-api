<?php

namespace App\Transformers;

use App\Models\UserHasCoupon;
use League\Fractal\TransformerAbstract;

class UserHasCouponTransformer extends TransformerAbstract
{

    public function transform(UserHasCoupon $coupon)
    {
        return [
            'id' => $coupon->id,
            'coupon_id' => $coupon->coupon_id,
            'coupon_snapshot' => $coupon->coupon_snapshot,
            'starting_price' => $coupon->starting_price,
            'type' => $coupon->type,
            'face_value' => $coupon->face_value,
            'used_at' => $coupon->used_at,
            'status' => $coupon->status,
            'discount_money' => $coupon->discount_money,
            'description' => $coupon->description,
            'created_at' => $coupon->created_at->toDatetimeString(),
        ];
    }
}