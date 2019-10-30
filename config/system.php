<?php
return [
    // 退款退货收快递地址信息
    'express_address' => [
        'name' => '东莞市韩苡琳科技有限公司',
        'mobile' => '13798829111',
        'address' => '东莞市东坑镇东坑丰收路1号201室'
    ],
    // 寄快递公司id
    'express_company_id' => env('EXPRESS_COMPANY_ID', 1),

    // 资讯关联
    'articles' => [
        'about_us' => 1,
        'join_us' => 2,
    ],

    // 订单发货后N天自动结算
    'order_auto_receive_days' => env('ORDER_AUTO_RECEIVE_DAYS', 7),
];