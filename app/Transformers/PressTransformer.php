<?php

namespace App\Transformers;

use App\Models\Press as Model;
use League\Fractal\TransformerAbstract;

class PressTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
        ];
    }
}