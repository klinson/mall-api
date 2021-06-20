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
        'about_us' => 1, //关于我们
        'join_us' => 2,  // 入驻我们
        'lottery_intro' => 3, //抽奖公告
        'coffer_intro' => 4, //金库公告
    ],

    // 订单发货后N天自动到货
    'order_auto_receive_days' => env('ORDER_AUTO_RECEIVE_DAYS', 7),
    // 订单到货后N天后不能发起退货退款
    'order_cannot_refund_days' => env('ORDER_CANNOT_REFUND_DAYS', 7),
    // 订单待结算后N天自动结算
    'order_auto_settle_days' => env('ORDER_AUTO_SETTLE_DAYS', 7),

    // 启动抽奖
    'enabled_lottery' => env('ENABLED_LOTTERY', 1),
    // 谢谢参与抽奖的权值
    'non_prize_rate' => env('NON_PRIZE_RATE', 0),

    // 邀请购买佣金比例, 1=>0.01%,500=>5%,10000=>100%
    'invite_bonus_rate' => 500,

    // 积分汇率
    // 积分使用开关
    'integral_status' => 0,
    // 积分->钱汇率，0.01=>100积分*0.01等于1块钱
    'integral2money_rate' => 0.01,
    // 消费金额->积分汇率 1 => 1块钱*1等于1积分
    'money2integral_rate' => 1,

];