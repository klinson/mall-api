<?php

namespace App\Transformers;

use App\Models\Express as Model;
use App\Models\Express;
use League\Fractal\TransformerAbstract;

class ExpressTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Express $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'code' => $model->code,
        ];
    }
}