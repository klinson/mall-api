<?php

namespace App\Transformers;

use App\Models\MemberLevel as Model;
use League\Fractal\TransformerAbstract;

class MemberLevelTransformer extends TransformerAbstract
{
    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'logo_url' => $model->logo_url,
            'discount' => $model->discount,
            'is_fee_freight' => $model->is_fee_freight,
            'level' => $model->level,
            'score' => $model->score,
            'next_score' => $model->nextMemberLevel->score,
        ];
    }
}