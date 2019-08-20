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
            'province_id' => $model->province_id,
            'city_id' => $model->city_id,
            'district_id' => $model->district_id,
            'address' => $model->address,
            'created_at' => $model->created_at->toDatetimeString(),
        ];
    }
}