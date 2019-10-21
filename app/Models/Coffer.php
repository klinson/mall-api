<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use function EasyWeChat\Kernel\Support\get_client_ip;

class Coffer extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    use HasOwnerHelper;

    public function logs()
    {
        return $this->hasMany(CofferLog::class, 'user_id', 'user_id');
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

    public function withdrawals()
    {
        return $this->hasMany(CofferWithdrawal::class, 'user_id', 'user_id');
    }
}
