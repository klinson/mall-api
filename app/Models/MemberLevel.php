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
}
