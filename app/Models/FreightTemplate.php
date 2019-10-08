<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class FreightTemplate extends Model
{
    use SoftDeletes;

    // 包邮类型
    const pinkage_types = [
        '不包邮', '按金额包邮', '按件包邮'
    ];

    protected $casts = [
        'addresses' => 'array'
    ];

    public static function getTemplate(Address $address)
    {
        $query = static::query();
        $query->sort()->enabled();
        $query->whereRaw("JSON_CONTAINS (addresses->'$[*]', '\"{$address->city_code}\"', '$')");
        $model = $query->first();
        return $model;
    }

    /**
     * 计算运费
     * @param int $weight 商品总重量 kg
     * @param int $goods_count 商品总个数 个
     * @param int $goods_cost 商品总费用 分
     * @author klinson <klinson@163.com>
     * @return int|mixed 运费 分
     */
    public function compute($weight = 0, $goods_count = 0, $goods_cost = 0)
    {
        //包邮类型，0-不包，1按金额，2按件数
        switch ($this->pinkage_type) {
            case 1: //单位元
                if ($goods_cost >= $this->pinkage_number * 100) {
                    return 0;
                }
                break;
            case 2:  // 单位个
                if ($goods_count >= $this->pinkage_number) {
                    return 0;
                }
                break;
            default:
                break;
        }
        // 首重1kg费用
        $cost = $this->basic_cost;

        // 续重费用，1kg起算
        if ($weight > 1) {
            $cost = intval(strval($cost + ceil($weight - 1) * $this->continued_cost));
        }

        return $cost;
    }
}
