<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;

class WalletLog extends Model
{
    use HasOwnerHelper;

    public $timestamps = false;
    protected $fillable = ['balance', 'type', 'description', 'ip', 'created_at', 'data_id', 'data_type'];

    const type_text = [
        '消费', '充值'
    ];

    public function datatype()
    {
        return $this->morphTo('datatype', 'data_type', 'data_id');
    }
}
