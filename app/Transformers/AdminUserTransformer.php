<?php

namespace App\Transformers;

use App\Models\AdminUser as Model;
use League\Fractal\TransformerAbstract;

class AdminUserTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'username' => $model->username,
            'name' => $model->name,
        ];
    }
}