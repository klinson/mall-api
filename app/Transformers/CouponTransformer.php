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
            'face_value_text' => $coupon->face_value_text,
            'draw_started_at' => $coupon->draw_started_at,
            'draw_ended_at' => $coupon->draw_ended_at,
            'valid_started_at' => $coupon->valid_started_at,
            'valid_ended_at' => $coupon->valid_ended_at,
            'limit' => $coupon->limit,
            'quantity' => $coupon->quantity,
            'all_quantity' => $coupon->all_quantity,
            'has_enabled' => $coupon->has_enabled,
        ];
        if ($this->type === 'pivot_count') {
            $data['count'] = $coupon->pivot->count;
        }
        if ($this->type === 'my_coupons_count') {
            $data['my_coupons_count'] = $coupon->my_coupons_count;
        }

        return $data;
    }
}