<?php

namespace App\Transformers;

use App\Models\DiscountGoods;
use App\Models\Goods;
use App\Models\UserFavourGoods as Model;
use League\Fractal\TransformerAbstract;

class UserFavourGoodsTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['favourGoods'];

    public function __construct()
    {
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'user_id' => $model->user_id,
            'goods_id' => $model->goods_id,
            'goods_type' => $model->goods_type,
            'created_at' => $model->created_at,
        ];
    }

    public function includeFavourGoods(Model $model)
    {
        switch ($model->goods_type) {
            case Goods::class:
                return $this->item($model->favourGoods, new GoodsTransformer('list'));
            case DiscountGoods::class:
                return $this->item($model->favourGoods, new DiscountGoodsTransformer());
            default:
                return null;
        }
    }
}