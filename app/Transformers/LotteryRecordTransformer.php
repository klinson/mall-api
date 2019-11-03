<?php

namespace App\Transformers;

use App\Models\LotteryRecord as Model;
use League\Fractal\TransformerAbstract;

class LotteryRecordTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['owner'];

    protected $type;
    public function __construct($type = '')
    {
        $this->type = $type;
    }

    public function transform(Model $model)
    {
        if ($this->type == 'hidden') {
            return [
                'id' => $model->id,
                'prize_id' => $model->prize_id,
                'prize_snapshot' => $model->prize_snapshot,
                'user_id' => $model->user_id,
                'created_at' => $model->created_at->toDateTimeString(),
            ];
        } else {
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

    public function includeOwner(Model $model)
    {
        return $this->item($model->owner, new UserTransformer($this->type));
    }
}