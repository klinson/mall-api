<?php

namespace App\Transformers;

use App\Models\Author as Model;
use League\Fractal\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
        ];
    }
}