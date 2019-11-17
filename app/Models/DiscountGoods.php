<?php

namespace App\Models;

use App\Transformers\DiscountGoodsTransformer;
use App\Transformers\GoodsSpecificationTransformer;
use App\Transformers\GoodsTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DiscountGoods extends Model
{
    use SoftDeletes;

    protected $casts = [
        'images' => 'array',
        'tags' => 'array'
    ];

    const THUMBNAIL_TEMPLATE = 'images/template.jpg';

    public function shoppingCarts()
    {
        return $this->morphMany(ShoppingCart::class, 'marketing', 'marketing_type', 'marketing_id', 'id');
    }

    public function orderGoods()
    {
        return $this->morphMany(OrderGoods::class, 'marketing', 'marketing_type', 'marketing_id', 'id');
    }

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

    public function check($goods_id, $specification_id)
    {
        if ($this->goods_id != $goods_id || $this->goods_specification_id != $specification_id) {
            return false;
        }
        return true;
    }

    // 售出减库存
    public function sold($quantity = 1, $is_refund = false)
    {
        try {
            // 减库存
            $query = DB::table('discount_goods')->where(
                $this->getKeyName(), $this->getKey()
            );

            if ($is_refund) {
                $columns = [
                    'quantity' => DB::raw("`quantity` + $quantity"),
                    'sold_quantity' => DB::raw("`sold_quantity` - $quantity"),
                ];
            } else {
                $columns = [
                    'quantity' => DB::raw("`quantity` - $quantity"),
                    'sold_quantity' => DB::raw("`sold_quantity` + $quantity"),
                ];
            }

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
        $info = (new DiscountGoodsTransformer('show'))->transform($this);

        $transformer = new GoodsSpecificationTransformer();
        $info['specification'] = $transformer->transform($this->specification);

        $transformer = new GoodsTransformer('show');
        $info['goods'] = $transformer->transform($this->goods);
        return $info;
    }
}
