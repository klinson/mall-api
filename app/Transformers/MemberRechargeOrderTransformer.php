<?php

namespace App\Transformers;

use App\Models\MemberRechargeOrder as Model;
use League\Fractal\TransformerAbstract;

class MemberRechargeOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['memberLevel'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'order_number' => $model->order_number,
            'balance' => $model->balance,
            'user_id' => $model->user_id,
            'member_recharge_activity_id' => $model->member_recharge_activity_id,
            'member_recharge_activity_snapshot' => $model->member_recharge_activity_snapshot,
            'member_level_id' => $model->member_level_id,
            'member_level_snapshot' => $model->member_level_snapshot,
            'validity_started_at' => $model->validity_started_at,
            'validity_ended_at' => $model->validity_ended_at,
            'status' => $model->status,
            'payed_at' => $model->payed_at,
            'created_at' => $model->created_at->toDateTimeString(),
        ];
    }

    public function includeMemberLevel(Model $model)
    {
        return $this->item($model->memberLevel, new MemberLevelTransformer());
    }

}