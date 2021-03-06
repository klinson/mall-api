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

    // 下个登记
    public function nextMemberLevel()
    {
        return $this->memberLevel->nextMemberLevel();
    }

    // 加经验
    public function addScore($order)
    {
        $score = to_int($order->real_price * 0.01);
        $this->increment('balance', $score);
        if ($level = $this->nextMemberLevel()) {
            if ($level->score <= $this->balance) {
                $this->member_level_id = $level->id;
            }
        }
        $this->save();
        $this->log($score, $order, "订单（{$order->order_number}）获得{$score}经验", 1);

    }

    public function logs()
    {
        return $this->hasMany(UserScoreLog::class, 'user_id', 'user_id');
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
