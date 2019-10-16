<?php

namespace App\Transformers;

use App\Models\RefundOrder;
use League\Fractal\TransformerAbstract;

class RefundOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['order', 'orderGoods'];

    public function transform(RefundOrder $order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'order_number' => $order->order_number,
            'order_id' => $order->order_id,
            'order_goods_id' => $order->order_goods_id,
            'goods_id' => $order->goods_id,
            'goods_specification_id' => $order->goods_specification_id,
            'reason_text' => $order->reason_text,
            'reason_images' => $order->reason_images,
            'quantity' => $order->quantity,
            'price' => $order->price,
            'real_price' => $order->real_price,
            'real_refund_cost' => $order->real_refund_cost,
            'real_refund_balance' => $order->real_refund_balance,
            'freight_price' => $order->freight_price,
            'status' => $order->status,
            'reject_reason' => $order->reject_reason,
            'refund_order_number' => $order->refund_order_number,
            'express_id' => $order->express_id,
            'express_number' => $order->express_number,
            'expressed_at' => $order->expressed_at,
            'confirmed_at' => $order->confirmed_at,

            'created_at' => $order->created_at->toDatetimeString(),
            'used_balance' => $order->used_balance,
            'real_cost' => $order->real_cost,
            'cancel_order_number' => $order->cancel_order_number,
        ];
    }

    public function includeOrderGoods(RefundOrder $order)
    {
        return $this->item($order->orderGoods, new OrderGoodsTransformer());
    }

    public function includeOrder(RefundOrder $order)
    {
        return $this->item($order->order, new OrderTransformer());
    }
}