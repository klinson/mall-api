<?php

namespace App\Transformers;

use App\Models\Address as Model;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'province', 'city', 'district'
    ];

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

    public function includeProvince(Model $model)
    {
        if (! $model->province) return null;
        return $this->item($model->province, new Transformer());
    }

    public function includeCity(Model $model)
    {
        if (! $model->city) return null;
        return $this->item($model->city, new Transformer());
    }

    public function includeDistrict(Model $model)
    {
        if (! $model->district) return null;
        return $this->item($model->district, new Transformer());
    }
}