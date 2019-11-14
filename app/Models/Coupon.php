<?php

namespace App\Models;

use App\Transformers\CouponTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    public function toUser($user, $description, $count = 1)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }

        $item = [
            'coupon_snapshot' => (new CouponTransformer())->transform($this),
            'user_id' => $user_id,
            'status' => 1,
            'discount_money' => 0,
            'has_enabled' => 1,
            'description' => $description
        ];
        $list = [];
        while ($count--) {
            $list[] = $item;
        }
        return $this->userCoupons()->createMany($list);
    }

    // 系统赠送
    public function present($user, $count = 1)
    {
        $this->toUser($user, '系统赠送(API)', $count);
    }

    public function userCoupons()
    {
        return $this->hasMany(UserHasCoupon::class, 'coupon_id');
    }
}
