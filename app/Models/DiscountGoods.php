<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountGoods extends Model
{
    use SoftDeletes;

    protected $casts = [
        'images' => 'array',
        'tags' => 'array'
    ];

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
        }
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function specification()
    {
        return $this->belongsTo(GoodsSpecification::class, 'goods_specification_id');
    }
}
