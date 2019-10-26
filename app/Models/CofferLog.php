<?php

namespace App\Models;


use App\Models\Traits\HasOwnerHelper;

class CofferLog extends Model
{
    use HasOwnerHelper;

    public $timestamps = false;

    const type_text = [
        0 => '提现',
        1 => '记录待结算',
        2 => '结算',
        3 => '结算扣除',
    ];

    const agency_level = [
        '', '直推', '间推'
    ];

    protected $fillable = ['balance', 'type', 'description', 'ip', 'created_at', 'data_id', 'data_type', 'agency', 'agency_level'];

    protected $casts = [
        'agency' => 'array'
    ];
}
