<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsSpecification extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'thumbnail', 'price', 'quantity', 'sort', 'has_enabled'];

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(Category::THUMBNAIL_TEMPLATE);
        }
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
}
