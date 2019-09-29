<?php

namespace App\Models;

use App\Transformers\GoodsSpecificationTransformer;
use App\Transformers\GoodsTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class GoodsSpecification extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'thumbnail', 'price', 'quantity', 'sold_quantity', 'sort', 'has_enabled', 'weight'];

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

    public function sold($quantity = 1)
    {
        try {
            // 减库存
            $query = DB::table('goods_specifications')->where(
                $this->getKeyName(), $this->getKey()
            );

            $columns = [
                'quantity' => DB::raw("`quantity` - $quantity"),
                'sold_quantity' => DB::raw("`sold_quantity` + $quantity"),
            ];
            $res = $query->update($columns);
            if ($res === 1) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function toSnapshot()
    {
        $transformer = new GoodsSpecificationTransformer();
        $specification = $transformer->transform($this);

        $transformer = new GoodsTransformer('show');

        $specification['goods'] = $transformer->transform($this->goods);
        return $specification;
    }
}
