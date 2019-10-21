<?php

namespace App\Transformers;

use App\Models\Coffer as Model;
use League\Fractal\TransformerAbstract;

class CofferTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'balance' => $model->balance,
            'unsettle_balance' => $model->unsettle_balance,
        ];
    }
}