<?php

namespace App\Transformers;

use App\Models\Category as Model;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
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
        ];
    }
}