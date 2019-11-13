<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MemberRechargeActivity extends Model
{
    use SoftDeletes;

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    const VALIDITY_TYPE2METHODS = [
        1 => 'addDays',
        2 => 'addMonths',
        3 => 'addYears'
    ];

    const validity_type_text = [
        1 => '天',
        2 => '月',
        3 => '年',
        4 => '永久'
    ];
    const invite_award_mode_text = [
        1 => '固定佣金',
        2 => '比例佣金'
    ];

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
        }
    }


    public function getRealValidityTimeAttribute()
    {
        if ($this->validity_type == 4) {
            return self::validity_type_text[$this->validity_type];
        } else {
            return $this->validity_times . self::validity_type_text[$this->validity_type];
        }
    }


    // 实际佣金
    public function getInviteRealAwardAttribute()
    {
        if ($this->invite_award_mode == 1) {
            return $this->invite_award;
        } else {
            return intval(strval($this->invite_award * $this->recharge_threshold * 0.001));
        }
    }

    public function scopeLevelBy($query)
    {
        return $query->orderBy('level', 'desc');
    }

    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class);
    }

    public function orders()
    {
        return $this->hasMany(MemberRechargeOrder::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'member_recharge_activity_has_coupons', 'activity_id', 'coupon_id')->withPivot(['count']);
    }
}
