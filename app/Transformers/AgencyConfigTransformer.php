<?php

namespace App\Transformers;

use App\Models\AgencyConfig as Model;
use League\Fractal\TransformerAbstract;

class AgencyConfigTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'recharge_threshold' => $model->recharge_threshold,
            'direct_profit_mode' => $model->direct_profit_mode,
            'direct_profit' => $model->direct_profit,
            'indirect_profit_mode' => $model->indirect_profit_mode,
            'indirect_profit' => $model->indirect_profit,
            'direct_agency_mode' => $model->direct_agency_mode,
            'direct_agency' => $model->direct_agency,
            'indirect_agency_mode' => $model->indirect_agency_mode,
            'indirect_agency' => $model->indirect_agency,
        ];
    }
}