<?php

namespace App\Transformers;

use App\Models\LotteryRecord as Model;
use League\Fractal\TransformerAbstract;

class LotteryRecordTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'prize_id' => $model->prize_id,
            'prize_snapshot' => $model->prize_snapshot,
            'user_id' => $model->user_id,
            'chance_id' => $model->chance_id,
            'address_id' => $model->address_id,
            'address_snapshot' => $model->address_snapshot,
            'express_id' => $model->express_id,
            'express_number' => $model->express_number,
            'status' => $model->status,
            'created_at' => $model->created_at->toDateTimeString(),
        ];
    }
}