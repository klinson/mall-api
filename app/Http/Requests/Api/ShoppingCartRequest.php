<?php

namespace App\Http\Requests\Api;

use App\Rules\CheckGoodsSpecification;

class ShoppingCartRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (strtoupper($this->getMethod())) {
            case 'PUT':
                return [
                    'quantity' => ['required', 'numeric', 'min:1'],
                ];
                break;
            default:
                return [
                    'quantity' => ['required', 'numeric', 'min:1'],
                    'goods_id' => ['required'],
                    'goods_specification_id' => ['required', new CheckGoodsSpecification($this->get('goods_id', 0), $this->get('quantity', 0))],
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'goods_id' => '商品',
            'quantity' => '购买数量',
            'goods_specification_id' => '商品规格'
        ];
    }
}
