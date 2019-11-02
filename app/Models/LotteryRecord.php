<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotteryRecord extends Model
{
    use SoftDeletes, HasOwnerHelper;

}
