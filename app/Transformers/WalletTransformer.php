<?php

namespace App\Transformers;

use App\Models\Wallet as Model;
use League\Fractal\TransformerAbstract;

class WalletTransformer extends TransformerAbstract
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