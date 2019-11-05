<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MemberLevel extends Model
{
    use SoftDeletes;

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function scopeLevelBy($query)
    {
        return $query->orderBy('level', 'desc');
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return get_admin_file_url($this->logo);
        } else {
            return asset(static::THUMBNAIL_TEMPLATE);
        }
    }

    public function activities()
    {
        return $this->hasMany(MemberRechargeActivity::class, 'member_level_id');
    }

    public static function getMaxDiscount($reset = false)
    {
        $cache_key = 'member_best_discount';

        if ($reset || app()->isLocal()) cache()->delete($cache_key);

        return cache()->remember($cache_key, 10, function () {
            // 100->原价 10折
            $discount = 100;
            $member = self::enabled()->orderBy('discount', 'asc')->first();
            if ($member) {
                $discount = intval($member->discount);
            }
            return $discount;
        });
    }
}
