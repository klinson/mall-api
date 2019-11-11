<?php

namespace App\Transformers;

use App\Models\Coupon;
use League\Fractal\TransformerAbstract;

class CouponTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Coupon $coupon)
    {
        return [
            'id' => $coupon->id,
            'title' => $coupon->title,
            'starting_price' => $coupon->starting_price,
            'type' => $coupon->type,
            'face_value' => $coupon->face_value,
            'has_enabled' => $coupon->has_enabled,
        ];
    }
}