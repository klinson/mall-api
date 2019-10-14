<?php

namespace App\Transformers;

use App\Models\WalletLog as Model;
use League\Fractal\TransformerAbstract;

class WalletLogTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'balance' => $model->balance,
            'type' => $model->type,
            'data_type' => $model->data_type,
            'data_id' => $model->data_id,
            'description' => $model->description,
            'created_at' => $model->created_at,
        ];
    }
}