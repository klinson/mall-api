<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Prize extends Model
{
    use SoftDeletes;

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
        }
    }

    public function scopeLevelBy($query)
    {
        return $query->orderBy('level', 'desc');
    }
}
