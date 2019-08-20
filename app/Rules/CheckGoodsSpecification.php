<?php

namespace App\Rules;

use App\Models\Goods;
use Illuminate\Contracts\Validation\Rule;

class CheckGoodsSpecification implements Rule
{
    protected $goodsId;
    protected $checkQuantity = 0;
    protected $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($goods_id = 0, $checkQuantity = 0)
    {
        $this->goodsId = $goods_id;
        $this->checkQuantity = $checkQuantity;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! $goods = Goods::find($this->goodsId)) {
            $this->message = '商品不存在';
            return false;
        }
        if (! $goods->has_enabled) {
            $this->message = '商品不存在';
            return false;
        }

        if (! $specification = $goods->soldSpecifications()->where('id', $value)->first()) {
            $this->message = '商品规格不存在';
            return false;
        }
        if (! $specification->has_enabled) {
            $this->message = '商品不存在';
            return false;
        }

        if ($this->checkQuantity && ! $specification->quantity > $this->checkQuantity) {
            $this->message = '商品规格库存不足';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
