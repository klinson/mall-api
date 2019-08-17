<?php

namespace App\Transformers;

use App\Models\Goods as Model;
use League\Fractal\TransformerAbstract;

class GoodsTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'category_id' => $model->category_id,
            'title' => $model->title,
            'thumbnail_url' => $model->thumbnail_url,
            'max_price' => $model->max_price,
            'min_price' => $model->min_price,
            'has_recommended' => $model->has_recommended,
            'created_at' => $model->created_at->toDateTimeString(),
        ];
    }
}