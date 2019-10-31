<?php

namespace App\Transformers;

use App\Models\MemberLevel as Model;
use League\Fractal\TransformerAbstract;

class MemberLevelTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'logo_url' => $model->logo_url,
            'discount' => $model->discount,
            'level' => $model->level,
        ];
    }
}