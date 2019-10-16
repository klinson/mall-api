<?php

namespace App\Transformers;

use App\Models\OrderGoods;
use League\Fractal\TransformerAbstract;

class OrderGoodsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['goods', 'specification', 'refundOrder'];

    public function transform(OrderGoods $orderGoods)
    {
        return [
            'id' => $orderGoods->id,
            'order_id' => $orderGoods->order_id,
            'goods_id' => $orderGoods->goods_id,
            'goods_specification_id' => $orderGoods->goods_specification_id,
            'price' => $orderGoods->price,
            'quantity' => $orderGoods->quantity,
            'snapshot' => $orderGoods->snapshot,
            'refund_status' => $orderGoods->refund_status,
        ];
    }

    public function includeGoods(OrderGoods $orderGoods)
    {
        return $this->item($orderGoods->goods, new GoodsTransformer());
    }

    public function includeSpecification(OrderGoods $orderGoods)
    {
        if ($orderGoods->specification) {
            return $this->item($orderGoods->specification, new GoodsSpecificationTransformer());
        } else {
            return null;
        }
    }

    public function includeRefundOrder(OrderGoods $orderGoods)
    {
        if ($orderGoods->refundOrder) {
            return $this->item($orderGoods->refundOrder, new RefundOrderTransformer());
        } else {
            return null;
        }
    }
}