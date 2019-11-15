<?php

namespace App\Http\Requests\Api;

use App\Rules\CheckGoodsMarketing;
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
                    'goods_specification_id' => ['required'],
                    'marketing_type' => ['in:App\Models\DiscountGoods'],
                    'marketing_id' => [
                        'required_with:marketing_type',
                        new CheckGoodsMarketing(
                            $this->get('goods_id', 0),
                            $this->get('goods_specification_id', 0),
                            $this->get('marketing_type', ''),
                            $this->get('quantity', 0)
                        )
                    ]
                ];
                break;
        }
    }

    public function attributes()
    {
        return [
            'goods_id' => '商品',
            'quantity' => '购买数量',
            'goods_specification_id' => '商品规格',
            'marketing_type' => '促销商品类型',
            'marketing_id' => '促销商品',
        ];
    }
}
