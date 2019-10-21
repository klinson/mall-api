<?php

namespace App\Transformers;

use App\Models\CofferWithdrawal as Model;
use League\Fractal\TransformerAbstract;

class CofferWithdrawalTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'balance' => $model->balance,
            'status' => $model->status,
            'checked_at' => $model->checked_at,
            'created_at' => $model->created_at->toDatetimeString(),
        ];
    }
}