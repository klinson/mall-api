<?php

namespace App\Models;


use App\Models\Traits\HasOwnerHelper;

class CofferLog extends Model
{
    use HasOwnerHelper;

    public $timestamps = false;

    protected $fillable = ['balance', 'type', 'description', 'ip', 'created_at', 'data_id', 'data_type'];

}
