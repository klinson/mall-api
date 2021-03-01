<?php

namespace App\Transformers;

use App\Models\Store as Model;
use League\Fractal\TransformerAbstract;

class StoreTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'thumbnail' => $model->thumbnail_url,
            'address' => $model->address,
            'latitude' => $model->latitude,
            'longitude' => $model->longitude,
            'distance' => 0,
        ];
    }
}