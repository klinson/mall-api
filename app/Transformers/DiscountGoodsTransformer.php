<?php

namespace App\Transformers;

use App\Models\Goods as Model;
use App\Models\MemberLevel;
use League\Fractal\TransformerAbstract;

class DiscountGoodsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['goods', 'specification'];

    protected $type;
    protected $user;

    public function __construct($type = 'list')
    {
        $this->type = $type;
        if ($type == 'show' && \Auth::check()) {
            $this->user = \Auth::user();
        }
    }

    public function transform(Model $model)
    {
        if ($this->type === 'show') {
            return [
                'id' => $model->id,
                'category_id' => $model->category_id,
                'title' => $model->title,
                'thumbnail_url' => $model->thumbnail_url,
                'price' => $model->price,
                'has_enabled' => $model->has_enabled,
                'created_at' => $model->created_at->toDateTimeString(),
                'images' => $model->images_url,
                'detail' => $model->detail,
                'is_favour' => ($this->user && $this->user->isMyFavourGoods($model->id)) ? 1 : 0,
            ];
        } else {
            return [
                'id' => $model->id,
                'category_id' => $model->category_id,
                'title' => $model->title,
                'thumbnail_url' => $model->thumbnail_url,
                'price' => $model->price,
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
}