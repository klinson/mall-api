<?php

namespace App\Transformers;

use App\Models\GoodsSpecification as Model;
use League\Fractal\TransformerAbstract;

class GoodsSpecificationTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'thumbnail_url' => $model->thumbnail_url,
            'quantity' => $model->quantity,
            'sold_quantity' => $model->sold_quantity,
            'price' => $model->price,
            'weight' => $model->weight,
        ];
    }
}