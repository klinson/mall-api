<?php

namespace App\Transformers;

use App\Models\Prize as Model;
use League\Fractal\TransformerAbstract;

class PrizeTransformer extends TransformerAbstract
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
            'origin_quantity' => $model->origin_quantity,
            'quantity' => $model->real_quantity,
            'price' => $model->price,
            'level' => $model->level,
        ];
    }
}