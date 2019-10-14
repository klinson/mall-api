<?php

namespace App\Models;

class WalletLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['balance', 'type', 'description', 'ip', 'created_at', 'data_id', 'data_type'];
}
