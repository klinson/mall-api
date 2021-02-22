<?php

namespace App\Transformers;

use App\Models\Order;
use App\Models\RechargeThresholdOrder;
use App\Models\WalletActivity;
use League\Fractal\TransformerAbstract;

class RechargeThresholdOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['agencyConfig'];

    public function transform(RechargeThresholdOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'order_number' => $order->order_number,
            'agency_config_id' => $order->agency_config_id,
            'status' => $order->status,
            'balance' => $order->balance,
            'result' => $order->result,
            'wallet_activity_snapshot' => WalletActivity::transformBySnapshot($order->wallet_activity_snapshot),
            'payed_at' => $order->payed_at,
            'created_at' => $order->created_at->toDatetimeString(),
        ];
    }

    public function includeAgencyConfig(RechargeThresholdOrder $order)
    {
        if ($order->agencyConfig->id) {
            return $this->item($order->agencyConfig, new AgencyConfigTransformer());
        } else {
            return null;
        }
    }
}