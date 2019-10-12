<?php

namespace App\Transformers;

use App\Models\Order;
use App\Models\RechargeThresholdOrder;
use League\Fractal\TransformerAbstract;

class RechargeThresholdOrderTransformer extends TransformerAbstract
{
    public function transform(RechargeThresholdOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'order_number' => $order->order_number,
            'agency_config_id' => $order->agency_config_id,
            'status' => $order->status,
            'balance' => $order->balance,
            'payed_at' => $order->payed_at,
            'created_at' => $order->created_at->toDatetimeString(),
        ];
    }

    public function includeOrderGoods(Order $order)
    {
        return $this->collection($order->orderGoods, new OrderGoodsTransformer());
    }

    public function includeUser(Order $order)
    {
        return $this->item($order->user, new UserTransformer());
    }

    public function includeCoupon(Order $order)
    {
        if ($order->coupon) {
            return $this->item($order->coupon, new UserCouponTransformer());
        } else {
            return null;
        }
    }

    public function includeLeaderModel(Order $order)
    {
        if ($order->leaderModel) {
            return $this->item($order->leaderModel, new UserTransformer('leader'));
        } else {
            return null;
        }
    }
}