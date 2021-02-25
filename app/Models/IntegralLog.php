<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;

class IntegralLog extends Model
{
    use HasOwnerHelper;

    public $timestamps = false;
    protected $fillable = ['balance', 'type', 'description', 'ip', 'created_at', 'data_id', 'data_type'];

    const type_text = [
        '消费', '激励'
    ];


}
