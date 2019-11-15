<?php

namespace App\Rules;

use App\Models\DiscountGoods;
use App\Models\Goods;
use Illuminate\Contracts\Validation\Rule;

class CheckGoodsMarketing implements Rule
{
    protected $goodsId;
    protected $goodsSpecificationId;
    protected $marketingType;
    protected $checkQuantity = 0;
    protected $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($goods_id = 0, $goods_specification_id = 0, $marketing_type = '', $checkQuantity = 0)
    {
        $this->goodsId = $goods_id;
        $this->goodsSpecificationId = $goods_specification_id;
        $this->marketingType = $marketing_type;
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
        if (empty($value) && empty($this->marketingType)) {
            return true;
        }
        if (! $goods = Goods::find($this->goodsId)) {
            $this->message = '商品不存在';
            return false;
        }

        if (! $specification = $goods->specifications()->where('id', $this->goodsSpecificationId)->first()) {
            $this->message = '商品规格不存在';
            return false;
        }

        $Class = $this->marketingType;
        if (! $marketingGoods = $Class::find($value)) {
            $this->message = '促销商品不存在';
            return false;
        }

        if ($marketingGoods->goods_id != $this->goodsId || $marketingGoods->goods_specification_id != $this->goodsSpecificationId) {
            $this->message = '促销商品不存在';
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
