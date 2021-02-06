<?php

namespace App\Models;

use App\Admin\Controllers\Traits\HasThumbnail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lvht\GeoHash;

class Store extends Model
{
    use SoftDeletes, HasThumbnail;
    const cache_key = 'list:stores';

    protected $fillable = ['title', 'address', 'longitude', 'latitude', 'point', 'geohash', 'thumbnail'];

    public function setPointAttribute($value)
    {
        $this->attributes['point'] = \DB::raw("ST_GeomFromText ('POINT({$this->longitude} {$this->latitude})')");
    }

    public function setGeohashAttribute($value)
    {
        $this->attributes['geohash'] =  GeoHash::encode($this->longitude, $this->latitude, 0.000001);
    }

    public static function getAllData()
    {
        return static::enabled()->orderBy('sort', 'desc')->get();
    }

    public function formatToArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'thumbnail' => $this->thumbnail_url,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance' => 0,
        ];
    }
}
