<?php

namespace App\Transformers;

use App\Models\Address as Model;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'mobile' => $model->mobile,
            'province_code' => $model->province_code,
            'city_code' => $model->city_code,
            'district_code' => $model->district_code,
            'address' => $model->address,
            'is_default' => $model->is_default,
            'created_at' => $model->created_at->toDatetimeString(),
        ];
    }
}