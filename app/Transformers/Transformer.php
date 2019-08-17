<?php

namespace App\Transformers;

use App\Models\Wallet;
use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{
    public function transform($model)
    {
        return json_decode(json_encode($model), true);
    }
}