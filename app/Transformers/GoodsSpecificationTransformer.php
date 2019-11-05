<?php

namespace App\Transformers;

use App\Models\GoodsSpecification as Model;
use App\Models\MemberLevel;
use League\Fractal\TransformerAbstract;

class GoodsSpecificationTransformer extends TransformerAbstract
{
    protected $discount = 100;

    public function __construct()
    {
        if (\Auth::check()) {
            $this->discount = \Auth::user()->getBestMemberDiscount();
        } else {
            $this->discount = MemberLevel::getMaxDiscount();
        }
    }

    public function transform(Model $model)
    {
        return [
            'id' => $model->id,
            'title' => $model->title,
            'thumbnail_url' => $model->thumbnail_url,
            'quantity' => $model->quantity,
            'sold_quantity' => $model->sold_quantity,
            'price' => $model->price,
            'discount_price' => $this->discount < 100 ? ceil(strval($model->price * $this->discount * 0.01)) : $model->price,
            'weight' => $model->weight,
        ];
    }
}