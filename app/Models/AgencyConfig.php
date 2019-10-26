<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyConfig extends Model
{
    use SoftDeletes;

    public function orders()
    {
        return $this->hasMany(RechargeThresholdOrder::class, 'agency_config_id', 'id');
    }

    public function unsettleOrder($order, $type = 'direct')
    {
        $balance = 0;
        $mode = $type.'_profit_mode';
        $profit = $type.'_profit';
        // 1-固定利润，2-售价折扣，3-利润折扣
        switch ($this->$mode) {
            case 1:
                $balance = $this->$profit * $this->goods_count;
                break;
            case 2:
                $balance = intval(strval($this->$profit * $order->goods_price * 0.01));
                break;
            case 3:
            default:
                break;
        }

        return $balance;
    }

    public function unsettleOrderGoods($orderGoods, $type = 'direct')
    {
        $balance = 0;
        $mode = $type.'_profit_mode';
        $profit = $type.'_profit';
        // 1-固定利润，2-售价折扣，3-利润折扣
        switch ($this->$mode) {
            case 1:
                $balance = $this->$profit * $this->goods_count;
                break;
            case 2:
                $balance = strval(strval($this->$profit * $orderGoods->price * $orderGoods->quantity * 0.01));
                break;
            case 3:
            default:
                break;
        }

        return $balance;
    }
}
