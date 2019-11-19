<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class AgencyConfig extends Model
{
    use SoftDeletes;

    const mode_text = [
        1 => '固定金额利润',
        2 => '比例提成佣金',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'agency_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(RechargeThresholdOrder::class, 'agency_config_id', 'id');
    }

    public function formatShow($mode, $profit)
    {
        switch ($mode) {
            case 1:
                $show = '固定利润￥'.strval($profit*0.01);
                break;
            case 2:
                $show = "提成{$profit}%";
                break;
            case 3:
            default:
                $show = '';
                break;
        }
        return $show;
    }

    public function getDirectProfitShowAttribute()
    {
        return $this->formatShow($this->direct_profit_mode, $this->direct_profit);
    }
    public function getIndirectProfitShowAttribute()
    {
        return $this->formatShow($this->indirect_profit_mode, $this->indirect_profit);
    }
    public function getDirectAgencyShowAttribute()
    {
        return $this->formatShow($this->direct_agency_mode, $this->direct_agency);
    }
    public function getIndirectAgencyShowAttribute()
    {
        return $this->formatShow($this->indirect_agency_mode, $this->indirect_agency);
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
                $balance = intval(strval($this->$profit * $orderGoods->price * $orderGoods->quantity * 0.01));
                break;
            case 3:
            default:
                break;
        }

        return $balance;
    }
}
