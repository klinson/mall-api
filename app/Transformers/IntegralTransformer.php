<?php

namespace App\Transformers;

use App\Models\Integral as Model;
use League\Fractal\TransformerAbstract;

class IntegralTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'balance' => $model->balance,
        ];
    }
}