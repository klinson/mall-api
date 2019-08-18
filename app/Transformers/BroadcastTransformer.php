<?php

namespace App\Transformers;

use App\Models\Broadcast as Model;
use League\Fractal\TransformerAbstract;

class BroadcastTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'content' => $model->content,
        ];
    }
}