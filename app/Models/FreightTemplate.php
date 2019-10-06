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

    // 续重类型
    const continued_types = [
        1 => '500g',
        2 => '1kg'
    ];

    protected $casts = [
        'addresses' => 'array'
    ];


}
