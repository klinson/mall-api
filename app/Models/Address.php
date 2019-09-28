<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'mobile', 'province_code', 'city_code', 'district_code', 'address', 'is_default'];

    public static function boot()
    {
        self::saved(function ($model) {
            if ($model->is_default) {
                self::where('user_id', $model->user_id)
                    ->where('id', '<>', $model->id)
                    ->update(['is_default' => 0]);
            }
        });

        parent::boot();
    }
}
