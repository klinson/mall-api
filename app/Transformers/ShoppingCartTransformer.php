<?php

namespace App\Transformers;

use App\Models\DiscountGoods;
use App\Models\ShoppingCart;
use League\Fractal\TransformerAbstract;

class ShoppingCartTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['goods', 'specification', 'marketing'];

    public function transform(ShoppingCart $shoppingCart)
    {
        return [
            'id' => $shoppingCart->id,
            'quantity' => $shoppingCart->quantity,
            'goods_id' => $shoppingCart->goods_id,
            'goods_specification_id' => $shoppingCart->goods_specification_id,
            'marketing_id' => $shoppingCart->marketing_id,
            'marketing_type' => $shoppingCart->marketing_type,
            'inviter_id' => $shoppingCart->inviter_id,
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

    public function includeMarketing(ShoppingCart $shoppingCart)
    {
        if ($shoppingCart->marketing) {
            $transformer = MARKETING2TRANSFORMER[$shoppingCart->marketing_type];
            return $this->item($shoppingCart->marketing, new $transformer());
        } else {
            return null;
        }
    }

}