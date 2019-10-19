<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Express extends Model
{
    use SoftDeletes;

    public static function getNameByCode($code)
    {
        $model = self::where('code', $code)->first();
        if ($model) {
            return $model->name;
        } else {
            return '未知物流公司';
        }
    }
}
