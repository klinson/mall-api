<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class CofferWithdrawal extends Model
{
    use HasOwnerHelper, SoftDeletes;

    protected $fillable = ['balance', 'status', 'ip'];

    // 1-申请中，2申请通过，3驳回
    const status_text = [
        1 => '审核中',
        2 => '申请通过',
        3 => '驳回',
    ];
}
