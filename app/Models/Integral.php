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

    /**
     * 积分变动
     * @param $order
     * @param int $type 0消费，1订单获得，2订单退回，99系统发放
     * @throws \Exception
     * @author klinson <klinson@163.com>
     */
    public function useIt($order, $type = 0)
    {
        if ($order->used_integral > 0) {
            if ($type == 0) {
                if ($this->balance < $order->used_integral) {
                    throw new \Exception('积分不足');
                }
                $this->decrement('balance', $order->used_integral);
                if (! $this->save()) {
                    throw new \Exception('积分扣除失败');
                }
                $this->log($order->used_integral, $order, "支付订单（{$order->order_number}）使用{$order->used_integral}积分");
            } else if ($type == 2) {
                $this->increment('balance', $order->used_integral);
                $this->save();
                $this->log($order->used_integral, $order, "订单（{$order->order_number}）退回{$order->used_integral}积分", $type);
            } else {
                $this->increment('balance', $order->used_integral);
                $this->save();
                $this->log($order->used_integral, $order, "订单（{$order->order_number}）获得{$order->used_integral}积分", $type);
            }
        }
    }

    public function log($balance, $model, $description, $type = 0)
    {
        $this->logs()->create([
            'balance' => $balance,
            'type' => $type,
            'data_id' => $model ? $model->id : 0,
            'data_type' => $model ? get_class($model) : null,
            'description' => $description,
            'ip' => ip2long(get_client_ip()),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
