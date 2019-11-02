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

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
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


}
