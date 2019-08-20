<?php

namespace App\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['orderGoods', 'user', 'specifications', 'coupon', 'leaderModel'];

    public function transform(Order $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'order_number' => $order->order_number,
            'all_price' => $order->all_price,
            'goods_price' => $order->goods_price,
            'real_price' => $order->real_price,
            'coupon_price' => $order->coupon_price,
            'freight_price' => $order->freight_price,
            'remarks' => $order->remarks,
            'status' => $order->status,
            'address_id' => $order->address_id,
            'created_at' => $order->created_at->toDatetimeString(),
            'payed_at' => $order->payed_at,
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