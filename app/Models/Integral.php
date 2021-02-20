<?php

namespace App\Models;

use function EasyWeChat\Kernel\Support\get_client_ip;

class Integral extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    public function logs()
    {
        return $this->hasMany(IntegralLog::class, 'user_id', 'user_id');
    }

    public function log($balance, $model, $description, $type = 0)
    {
        $this->logs()->create([
            'balance' => $balance,
            'type' => $type,
            'data_id' => $model->id,
            'data_type' => get_class($model),
            'description' => $description,
            'ip' => ip2long(get_client_ip()),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
