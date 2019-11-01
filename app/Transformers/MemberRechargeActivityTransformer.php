<?php

namespace App\Transformers;

use App\Models\MemberRechargeActivity as Model;
use League\Fractal\TransformerAbstract;

class MemberRechargeActivityTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['memberLevel'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'thumbnail_url' => $model->thumbnail_url,
            'member_level_id' => $model->member_level_id,
            'validity_type' => $model->validity_type,
            'validity_times' => $model->validity_times,
            'recharge_threshold' => $model->recharge_threshold,
            'level' => $model->level,
            'invite_real_award' => $model->invite_real_award,
            'has_enabled' => $model->has_enabled,
            'created_at' => $model->created_at->toDateTimeString(),
        ];
    }

    public function includeMemberLevel(Model $model)
    {
        return $this->item($model->memberLevel, new MemberLevelTransformer());
    }

}