<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;

class UserScoreLog extends Model
{
    use HasOwnerHelper;

    public $timestamps = false;
    protected $fillable = ['balance', 'type', 'description', 'ip', 'created_at', 'data_id', 'data_type'];

    const type_text = [
        '扣除', '加分'
    ];

    public function datatype()
    {
        return $this->morphTo('datatype', 'data_type', 'data_id');
    }
}
