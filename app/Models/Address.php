<?php

namespace App\Models;

use App\Transformers\AddressTransformer;
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

    public function getAllCityNameAttribute()
    {
        return $this->district->full_name;
    }

    public function toSnapshot()
    {
        $transformer = new AddressTransformer();
        $info = $transformer->transform($this);
        $info['city_name'] = $this->all_city_name;
        return $info;
    }

    public function district()
    {
        return $this->belongsTo(Area::class, 'district_code', 'code');
    }
}
