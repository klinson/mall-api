<?php

namespace App\Transformers;

use App\Models\WalletActivity as Model;
use League\Fractal\TransformerAbstract;

class WalletActivityTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'threshold' => $model->threshold,
            'present' => $model->present,
        ];
    }
}