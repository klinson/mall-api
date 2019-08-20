<?php

namespace App\Transformers;

use App\Models\ShoppingCart;
use League\Fractal\TransformerAbstract;

class ShoppingCartTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['goods', 'specification'];

    public function transform(ShoppingCart $shoppingCart)
    {
        return [
            'id' => $shoppingCart->id,
            'quantity' => $shoppingCart->quantity,
            'goods_id' => $shoppingCart->goods_id,
            'goods_specification_id' => $shoppingCart->goods_specification_id,
        ];
    }

    public function includeGoods(ShoppingCart $shoppingCart)
    {
        return $this->item($shoppingCart->goods, new GoodsTransformer());
    }

    public function includeSpecification(ShoppingCart $shoppingCart)
    {
        return $this->item($shoppingCart->specification, new GoodsSpecificationTransformer());
    }

}