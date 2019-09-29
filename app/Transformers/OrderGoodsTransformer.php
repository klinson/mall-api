<?php

namespace App\Transformers;

use App\Models\OrderGoods;
use League\Fractal\TransformerAbstract;

class OrderGoodsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['goods', 'specification'];

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
}