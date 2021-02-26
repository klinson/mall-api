<?php

namespace App\Models;

use App\Transformers\AddressTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'mobile', 'province_code', 'city_code', 'district_code', 'address', 'is_default', 'city_name'];

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

    public function setCityNameAttribute($value)
    {
        if ($this->district) {
            $name = $this->district->full_name;
        } else {
            $name = $this->city->full_name;
        }
        $this->attributes['city_name'] = $name;
    }

    public function toSnapshot()
    {
        $transformer = new AddressTransformer();
        $info = $transformer->transform($this);
        $info['city_name'] = $this->all_city_name;
        return $info;
    }

    public function province()
    {
        return $this->belongsTo(Area::class, 'province_code', 'code');
    }

    public function district()
    {
        return $this->belongsTo(Area::class, 'district_code', 'code');
    }

    public function city()
    {
        return $this->belongsTo(Area::class, 'city_code', 'code');
    }
}
