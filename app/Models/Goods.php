<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model
{
    use SoftDeletes;

    protected $casts = [
        'images' => 'array'
    ];

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    protected $fillable = ['title', 'isbn', 'press_id', 'category_id', 'images', 'thumbnail'];

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return get_admin_file_url($this->thumbnail);
        } else {
            return asset(self::THUMBNAIL_TEMPLATE);
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withDefault([
            'title' => '【无分类】'
        ]);
    }

    public function specifications()
    {
        return $this->hasMany(GoodsSpecification::class, 'goods_id')->orderBy('sort');
    }

    public function soldSpecifications()
    {
        return $this->specifications()->where('has_enabled', 1);
    }

    public function getImagesUrlAttribute()
    {
        if (! empty($this->images)) {
            return get_admin_file_urls($this->images);
        } else {
            return [];
        }
    }

    public function autoUpdate()
    {
        $specifications = $this->soldSpecifications;
        $this->max_price = $specifications->max('price');
        $this->min_price = $specifications->min('price');
        $this->save();
    }

    public function getAdCodeAttribute()
    {
        return 'goods-'.$this->id;
    }

    public function favourUsers()
    {
        return $this->morphMany(UserFavourGoods::class, 'favourGoods', 'goods_type', 'goods_id');
    }

    public function discountGoods()
    {
        return $this->hasMany(DiscountGoods::class, 'goods_id');
    }

    public function soldDiscountGoods()
    {
        return $this->discountGoods()->where('has_enabled', 1);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'goods_has_authors', 'goods_id', 'author_id');
    }

    // 出版社
    public function press()
    {
        return $this->belongsTo(Press::class, 'press_id');
    }

}
