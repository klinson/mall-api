<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class CofferWithdrawal extends Model
{
    use HasOwnerHelper, SoftDeletes;

    protected $fillable = ['balance', 'status', 'ip'];
}
