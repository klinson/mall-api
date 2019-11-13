<?php

namespace App\Transformers;

use App\Models\Coupon;
use League\Fractal\TransformerAbstract;

class CouponTransformer extends TransformerAbstract
{
    protected $type;
    public function __construct($type = '')
    {
        $this->type = $type;
    }

    public function transform(Coupon $coupon)
    {
        $data = [
            'id' => $coupon->id,
            'title' => $coupon->title,
            'start_price' => $coupon->start_price,
            'type' => $coupon->type,
            'face_value' => $coupon->face_value,
            'has_enabled' => $coupon->has_enabled,
        ];
        if ($this->type === 'pivot_count') {
            $data['count'] = $coupon->pivot->count;
        }

        return $data;
    }
}