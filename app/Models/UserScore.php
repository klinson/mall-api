<?php

namespace App\Models;

use function EasyWeChat\Kernel\Support\get_client_ip;

class UserScore extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id', 'member_level_id', 'balance'];

    public function memberLevel()
    {
        return $this->belongsTo(MemberLevel::class, 'member_level_id');
    }

    public function logs()
    {
        return $this->hasMany(WalletLog::class, 'user_id', 'user_id');
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
