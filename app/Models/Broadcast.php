<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Broadcast extends Model
{
    use SoftDeletes;

    public static function getShow()
    {
        return self::enabled()->orderBy('id', 'desc')->first();
    }
}
