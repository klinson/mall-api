<?php

use Illuminate\Database\Seeder;

class AdminMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_menu')->delete();
        
        \DB::table('admin_menu')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => 0,
                'order' => 1,
                'title' => '管理平台',
                'icon' => 'fa-dashboard',
                'uri' => '/',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2018-11-04 11:15:46',
            ),
            1 => 
            array (
                'id' => 3,
                'parent_id' => 0,
                'order' => 31,
                'title' => '系统管理',
                'icon' => 'fa-tasks',
                'uri' => '',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            2 => 
            array (
                'id' => 4,
                'parent_id' => 3,
                'order' => 32,
                'title' => '管理员管理',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            3 => 
            array (
                'id' => 5,
                'parent_id' => 3,
                'order' => 34,
                'title' => '管理员角色管理',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            4 => 
            array (
                'id' => 6,
                'parent_id' => 3,
                'order' => 35,
                'title' => '管理员角色权限管理',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            5 => 
            array (
                'id' => 7,
                'parent_id' => 3,
                'order' => 36,
                'title' => '系统菜单管理',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            6 => 
            array (
                'id' => 8,
                'parent_id' => 3,
                'order' => 39,
                'title' => '系统操作日志',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            7 => 
            array (
                'id' => 9,
                'parent_id' => 0,
                'order' => 2,
                'title' => '商城管理',
                'icon' => 'fa-copy',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            8 => 
            array (
                'id' => 10,
                'parent_id' => 9,
                'order' => 4,
                'title' => '分类管理',
                'icon' => 'fa-cubes',
                'uri' => 'categories',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            9 => 
            array (
                'id' => 11,
                'parent_id' => 9,
                'order' => 5,
                'title' => '商品管理',
                'icon' => 'fa-file-text',
                'uri' => 'goods',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            10 => 
            array (
                'id' => 12,
                'parent_id' => 3,
                'order' => 37,
                'title' => '资源管理',
                'icon' => 'fa-file',
                'uri' => 'media',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            11 => 
            array (
                'id' => 13,
                'parent_id' => 3,
                'order' => 33,
                'title' => '系统配置管理',
                'icon' => 'fa-toggle-on',
                'uri' => 'config',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            12 => 
            array (
                'id' => 14,
                'parent_id' => 3,
                'order' => 38,
                'title' => '备份管理',
                'icon' => 'fa-copy',
                'uri' => 'backup',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            13 => 
            array (
                'id' => 19,
                'parent_id' => 0,
                'order' => 10,
                'title' => '广告管理',
                'icon' => 'fa-adn',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-01-01 22:53:23',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            14 => 
            array (
                'id' => 20,
                'parent_id' => 19,
                'order' => 11,
                'title' => '轮播管理',
                'icon' => 'fa-caret-square-o-right',
                'uri' => 'carouselAds',
                'permission' => NULL,
                'created_at' => '2019-01-01 22:58:52',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            15 => 
            array (
                'id' => 21,
                'parent_id' => 19,
                'order' => 12,
                'title' => '广播管理',
                'icon' => 'fa-feed',
                'uri' => 'broadcasts',
                'permission' => NULL,
                'created_at' => '2019-08-18 00:09:15',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            16 => 
            array (
                'id' => 22,
                'parent_id' => 9,
                'order' => 7,
                'title' => '运费模板管理',
                'icon' => 'fa-truck',
                'uri' => 'freightTemplates',
                'permission' => NULL,
                'created_at' => '2019-10-08 09:04:11',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            17 => 
            array (
                'id' => 23,
                'parent_id' => 9,
                'order' => 8,
                'title' => '订单管理',
                'icon' => 'fa-file-text-o',
                'uri' => 'orders',
                'permission' => NULL,
                'created_at' => '2019-10-08 09:05:14',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            18 => 
            array (
                'id' => 24,
                'parent_id' => 9,
                'order' => 9,
                'title' => '售后申请管理',
                'icon' => 'fa-file-archive-o',
                'uri' => 'refundOrders',
                'permission' => NULL,
                'created_at' => '2019-10-16 00:00:48',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            19 => 
            array (
                'id' => 25,
                'parent_id' => 19,
                'order' => 13,
                'title' => '资讯管理',
                'icon' => 'fa-bars',
                'uri' => 'articles',
                'permission' => NULL,
                'created_at' => '2019-10-23 00:50:17',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            20 => 
            array (
                'id' => 26,
                'parent_id' => 0,
                'order' => 14,
                'title' => '用户管理',
                'icon' => 'fa-users',
                'uri' => 'users',
                'permission' => NULL,
                'created_at' => '2019-10-26 19:12:27',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            21 => 
            array (
                'id' => 29,
                'parent_id' => 0,
                'order' => 26,
                'title' => '抽奖管理',
                'icon' => 'fa-codepen',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-11-07 12:54:07',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            22 => 
            array (
                'id' => 30,
                'parent_id' => 29,
                'order' => 27,
                'title' => '奖品管理',
                'icon' => 'fa-gift',
                'uri' => 'prizes',
                'permission' => NULL,
                'created_at' => '2019-11-07 12:54:56',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            23 => 
            array (
                'id' => 31,
                'parent_id' => 29,
                'order' => 28,
                'title' => '中奖记录',
                'icon' => 'fa-file-archive-o',
                'uri' => 'lotteryRecords',
                'permission' => NULL,
                'created_at' => '2019-11-07 12:55:29',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            24 => 
            array (
                'id' => 32,
                'parent_id' => 29,
                'order' => 29,
                'title' => '抽奖机会',
                'icon' => 'fa-delicious',
                'uri' => 'lotteryChances',
                'permission' => NULL,
                'created_at' => '2019-11-07 12:56:14',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            25 => 
            array (
                'id' => 33,
                'parent_id' => 0,
                'order' => 15,
                'title' => '会员管理',
                'icon' => 'fa-user-md',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-11-07 12:56:59',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            26 => 
            array (
                'id' => 34,
                'parent_id' => 33,
                'order' => 16,
                'title' => '会员等级管理',
                'icon' => 'fa-bitbucket',
                'uri' => 'memberLevels',
                'permission' => NULL,
                'created_at' => '2019-11-07 12:57:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            27 => 
            array (
                'id' => 35,
                'parent_id' => 33,
                'order' => 17,
                'title' => '会员充值活动管理',
                'icon' => 'fa-archive',
                'uri' => 'memberRechargeActivities',
                'permission' => NULL,
                'created_at' => '2019-11-07 12:58:21',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            28 => 
            array (
                'id' => 36,
                'parent_id' => 33,
                'order' => 18,
                'title' => '会员充值订单管理',
                'icon' => 'fa-file-powerpoint-o',
                'uri' => 'memberRechargeOrders',
                'permission' => NULL,
                'created_at' => '2019-11-07 12:58:46',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            29 => 
            array (
                'id' => 37,
                'parent_id' => 0,
                'order' => 22,
                'title' => '财务管理',
                'icon' => 'fa-credit-card',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-11-07 23:52:20',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            30 => 
            array (
                'id' => 38,
                'parent_id' => 37,
                'order' => 23,
                'title' => '提现管理',
                'icon' => 'fa-sign-out',
                'uri' => 'cofferWithdrawals',
                'permission' => NULL,
                'created_at' => '2019-11-07 23:52:54',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            31 => 
            array (
                'id' => 39,
                'parent_id' => 0,
                'order' => 30,
                'title' => '系统设置',
                'icon' => 'fa-cogs',
                'uri' => 'system',
                'permission' => NULL,
                'created_at' => '2019-11-09 02:48:50',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            32 => 
            array (
                'id' => 40,
                'parent_id' => 0,
                'order' => 19,
                'title' => '优惠券管理',
                'icon' => 'fa-credit-card-alt',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-11-21 00:54:55',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            33 => 
            array (
                'id' => 41,
                'parent_id' => 40,
                'order' => 20,
                'title' => '优惠券管理',
                'icon' => 'fa-credit-card',
                'uri' => 'coupons',
                'permission' => NULL,
                'created_at' => '2019-11-21 00:55:16',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            34 => 
            array (
                'id' => 42,
                'parent_id' => 40,
                'order' => 21,
                'title' => '用户拥有优惠券管理',
                'icon' => 'fa-bars',
                'uri' => 'userHasCoupons',
                'permission' => NULL,
                'created_at' => '2019-11-21 00:55:35',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            35 => 
            array (
                'id' => 43,
                'parent_id' => 9,
                'order' => 6,
                'title' => '折扣秒杀商品管理',
                'icon' => 'fa-file-excel-o',
                'uri' => 'discountGoods',
                'permission' => NULL,
                'created_at' => '2019-11-22 22:00:07',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            36 => 
            array (
                'id' => 44,
                'parent_id' => 37,
                'order' => 24,
                'title' => '钱包日志',
                'icon' => 'fa-lemon-o',
                'uri' => 'walletLogs',
                'permission' => NULL,
                'created_at' => '2019-11-22 22:03:22',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            37 => 
            array (
                'id' => 45,
                'parent_id' => 37,
                'order' => 25,
                'title' => '金库日志',
                'icon' => 'fa-diamond',
                'uri' => 'cofferLogs',
                'permission' => NULL,
                'created_at' => '2019-11-22 22:03:40',
                'updated_at' => '2021-02-07 16:54:49',
            ),
            38 => 
            array (
                'id' => 46,
                'parent_id' => 9,
                'order' => 3,
                'title' => '门店管理',
                'icon' => 'fa-hospital-o',
                'uri' => 'stores',
                'permission' => NULL,
                'created_at' => '2021-02-07 16:54:43',
                'updated_at' => '2021-02-07 16:54:49',
            ),
        ));
        
        
    }
}