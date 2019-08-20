<?php

namespace App\Rules;

use App\Models\Goods;
use Illuminate\Contracts\Validation\Rule;

class CheckGoods implements Rule
{
    protected $message;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        if (! $goods = Goods::find($value)) {
            $this->message = '商品不存在';
            return false;
        }
        if (! $goods->has_enabled) {
            $this->message = '商品不存在';
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
