<?php

namespace App\Transformers;

use App\Models\Goods as Model;
use App\Models\MemberLevel;
use League\Fractal\TransformerAbstract;

class GoodsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['category', 'soldSpecifications', 'soldDiscountGoods', 'authors', 'press'];

    protected $type;
    protected $discount = 100;
    protected $user;

    public function __construct($type = 'list')
    {
        $this->type = $type;
        if (\Auth::check()) {
            if ($type == 'show') $this->user = \Auth::user();
            $this->discount = \Auth::user()->getBestMemberDiscount();
        } else {
            // 没登录没会员的默认显示优惠价
//            $this->discount = MemberLevel::getMaxDiscount();
        }
    }

    public function transform(Model $model)
    {
        if ($this->type === 'show') {
            return [
                'id' => $model->id,
                'category_id' => $model->category_id,
                'isbn' => $model->isbn,
                'title' => $model->title,
                'thumbnail_url' => $model->thumbnail_url,
                'description' => $model->description,
                'publish_date' => $model->publish_date,
                'max_price' => $model->max_price,
                'min_price' => $model->min_price,
                'discount_max_price' => $this->discount < 100 ? ceil(strval($model->max_price * $this->discount * 0.01)) : $model->max_price,
                'discount_min_price' => $this->discount < 100 ? ceil(strval($model->min_price * $this->discount * 0.01)) : $model->min_price,
                'has_recommended' => $model->has_recommended,
                'has_enabled' => $model->has_enabled,
                'created_at' => $model->created_at->toDateTimeString(),
                'images' => $model->images_url,
                'detail' => $model->detail,
                'is_favour' => ($this->user && $this->user->isMyFavourGoods($model)) ? 1 : 0,
            ];
        } else {
            return [
                'id' => $model->id,
                'category_id' => $model->category_id,
                'isbn' => $model->isbn,
                'title' => $model->title,
                'thumbnail_url' => $model->thumbnail_url,
                'max_price' => $model->max_price,
                'min_price' => $model->min_price,
                'description' => $model->description,
                'publish_date' => $model->publish_date,
                'discount_max_price' => $this->discount < 100 ? ceil(strval($model->max_price * $this->discount * 0.01)) : $model->max_price,
                'discount_min_price' => $this->discount < 100 ? ceil(strval($model->min_price * $this->discount * 0.01)) : $model->min_price,
                'has_recommended' => $model->has_recommended,
                'has_enabled' => $model->has_enabled,
                'created_at' => $model->created_at->toDateTimeString(),
            ];
        }
    }

    public function includeCategory(Model $model)
    {
        return $this->item($model->category, new CategoryTransformer());
    }

    public function includeSoldSpecifications(Model $model)
    {
        return $this->collection($model->soldSpecifications, new GoodsSpecificationTransformer());
    }

    public function includeSoldDiscountGoods(Model $model)
    {
        return $this->collection($model->soldDiscountGoods, new DiscountGoodsTransformer());
    }

    public function includePress(Model $model)
    {
        if ($model->press) return $this->item($model->press, new PressTransformer());
        else return null;
    }

    public function includeAuthors(Model $model)
    {
        return $this->collection($model->authors, new AuthorTransformer());
    }
}