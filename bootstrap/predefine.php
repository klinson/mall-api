<?php
/*
|--------------------------------------------------------------------------
| 预定义常量
|--------------------------------------------------------------------------
*/

const HAS_ENABLED2TEXT = [
    '禁用', '正常'
];

const YN2TEXT = ['否', '是'];

const MARKETING2TRANSFORMER = [
    \App\Models\DiscountGoods::class => \App\Transformers\DiscountGoodsTransformer::class
];
