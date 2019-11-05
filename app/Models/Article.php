<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    public function getAdCodeAttribute()
    {
        return 'article-'.$this->id;
    }
}
