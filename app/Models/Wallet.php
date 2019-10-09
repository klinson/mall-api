<?php

namespace App\Models;

use function EasyWeChat\Kernel\Support\get_client_ip;

class Wallet extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    public function logs()
    {
        return $this->hasMany(WalletLog::class, 'user_id', 'user_id');
    }

    public function log($balance, $description, $type = 0)
    {
        $this->logs()->create([
            'balance' => $balance,
            'type' => $type,
            'description' => $description,
            'ip' => ip2long(get_client_ip()),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
