<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserHasCoupon extends Model
{
    use SoftDeletes, HasOwnerHelper;

    const status_text = [
        1 => '可用',
        2 => '过期',
        3 => '已用',
        4 => '冻结'
    ];

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

    /**
     * 计算优惠金额
     * @param $price
     * @return int|mixed
     * @author klinson <klinson@163.com>
     */
    public function settleDiscount($price)
    {
        if ($price <= 0) return 0;

        if ($this->start_price > $price) {
            $discount_money = 0;
        } else {
            switch ($this->type) {
                case 1:
                    if ($this->face_value >= 100) {
                        $discount_money = 0;
                    } else {
                        $discount_money = $price - intval(strval(($this->face_value * $price * 0.01)));
                    }
                    break;
                case 2:
                    $discount_money = $this->face_value > $price ? $price : $this->face_value;
                    break;
                default:
                    $discount_money = 0;
                    break;
            }
        }
        return $discount_money;
    }

    // 冻结
    public function freeze()
    {
        if ($this->status !== 1) {
            return false;
        }
        $this->status = 4;
        return $this->save();
    }
    // 解结
    public function unfreeze()
    {
        if ($this->status !== 4) {
            return false;
        }
        $this->status = 1;
        return $this->save();
    }
    // 使用优惠券
    public function useIt($coupon_price)
    {
        // 仅在冻结状态下可使用
        if ($this->status !== 4) {
            return false;
        }
        $this->used_at = Carbon::now()->toDateTimeString();
        $this->discount_money = $coupon_price;
        $this->status = 3;
        return $this->save();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

}
