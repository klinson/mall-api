<?php

namespace App\Transformers;

use App\Models\UserHasMemberLevel as Model;
use League\Fractal\TransformerAbstract;

class UserHasMemberLevelTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'user_id' => $model->user_id,
            'level' => $model->level,
            'member_level_id' => $model->member_level_id,
            'member_level_snapshot' => $model->member_level_snapshot,
            'validity_started_at' => $model->validity_started_at,
            'validity_ended_at' => $model->validity_ended_at,
        ];
    }
}