<?php

namespace App\Transformers;

use App\Models\UserScore as Model;
use League\Fractal\TransformerAbstract;

class UserScoreTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['memberLevel'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'member_level_id' => $model->member_level_id,
            'balance' => $model->balance,
        ];
    }

    public function includeMemberLevel(Model $model)
    {
        return $this->item($model->memberLevel, new MemberLevelTransformer());
    }
}