<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Press extends Model
{
    use SoftDeletes;

    protected $fillable = ['title'];

}
