<?php

namespace App\Models;

use App\Transformers\CouponTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;
    const type_text = [
        1 => '折扣券',
        2 => '满减券',
    ];
    protected $appends = ['face_value_text'];

    public function getFaceValueTextAttribute()
    {
        // 2满减固定金额券，1打固定折扣券
        switch ($this->type) {
            case 1:
                return '打' . strval($this->face_value * 0.1) . '折';
            case 2:
                return '减￥' . strval($this->face_value * 0.01);
            default:
                return '';
        }
    }

    public function toUser($user, $description, $count = 1)
    {
        if ($user instanceof User) {
            $user_id = $user->id;
        } else {
            $user_id = intval($user);
        }

        $item = [
            'coupon_snapshot' => $this->toArray(),
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
