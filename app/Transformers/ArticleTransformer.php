<?php

namespace App\Transformers;

use App\Models\Article as Model;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'content' => $model->content,
        ];
    }
}