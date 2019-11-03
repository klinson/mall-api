<?php

namespace App\Models;

use App\Models\Traits\HasOwnerHelper;
use App\Transformers\AgencyConfigTransformer;
use function EasyWeChat\Kernel\Support\get_client_ip;
use DB;

class Coffer extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'user_id';

    use HasOwnerHelper;

    public function logs()
    {
        return $this->hasMany(CofferLog::class, 'user_id', 'user_id');
    }

    public function log($balance, $model, $type = 0, $agency = null, $agency_level = 0)
    {
        $description = $this->generateDescription($balance, $model, $type, $agency_level);

        $this->logs()->create([
            'balance' => $balance,
            'type' => $type,
            'data_id' => $model->id,
            'data_type' => get_class($model),
            'description' => $description,
            'ip' => ip2long(get_client_ip()),
            'created_at' => date('Y-m-d H:i:s'),
            'agency' => $agency ? (new AgencyConfigTransformer())->transform($agency) : [],
            'agency_level' => $agency_level
        ]);
    }

    /**
     * 生成描述
     * @param $balance
     * @param $model
     * @param int $type
     * @author klinson <klinson@163.com>
     * @return string
     */
    public function generateDescription($balance, $model, $type = 0, $agency_level = 0)
    {
        $balance = strval($balance * 0.01) . '元';
        switch (get_class($model)) {
            case Order::class:
                $desc = '【'.CofferLog::type_text[$type]."】【".CofferLog::agency_level[$agency_level]."】订单({$model->order_number})佣金{$balance}";
                break;
            case OrderGoods::class:
                $desc = '【'.CofferLog::type_text[$type]."】【".CofferLog::agency_level[$agency_level]."】订单({$model->order->order_number})中商品({$model->snapshot['goods']['title']}-{$model->snapshot['title']})佣金{$balance}";
                break;
            case RefundOrder::class:
                $desc = '【'.CofferLog::type_text[$type]."】【".CofferLog::agency_level[$agency_level]."】售后订单({$model->order_number})中商品({$model->orderGoods->snapshot['goods']['title']}-{$model->orderGoods->snapshot['title']})佣金{$balance}";
                break;
            case CofferWithdrawal::class:
                $desc = '【'.CofferLog::type_text[$type]."】订单({$model->order_number})金额{$model->balance}";
                break;
            case MemberRechargeOrder::class:
                $desc = '【'.CofferLog::type_text[$type]."】邀请会员订单({$model->order_number})佣金{$balance}";
                break;
            default:
                $desc = '';
                break;
        }

        return $desc;
    }

    public function unsettle($balance, $model, $agency, $agency_level)
    {
        if ($balance > 0) {
            $this->increment('unsettle_balance', $balance);
            $this->save();
        }

        $this->log($balance, $model, 1, $agency, $agency_level);
    }

    public function settleMemberRechargeOrder($balance, $model)
    {
        if ($balance > 0) {
            $this->increment('balance', $balance);
            $this->save();
        }

        $this->log($balance, $model, 2);
    }

    public function withdrawals()
    {
        return $this->hasMany(CofferWithdrawal::class, 'user_id', 'user_id');
    }

    /**
     * $columns = [
    'quantity' => DB::raw("`balance` + $balance"),
    'sold_quantity' => DB::raw("`unsettle_balance` - $balance"),
    ];
     */
}
