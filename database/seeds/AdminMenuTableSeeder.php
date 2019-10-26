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
                'id' => 2,
                'parent_id' => 3,
                'order' => 24,
                'title' => '用户管理',
                'icon' => 'fa-users',
                'uri' => 'users',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 0,
                'order' => 15,
                'title' => '系统管理',
                'icon' => 'fa-tasks',
                'uri' => '',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => 3,
                'order' => 16,
                'title' => '管理员管理',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            4 => 
            array (
                'id' => 5,
                'parent_id' => 3,
                'order' => 18,
                'title' => '管理员角色管理',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            5 => 
            array (
                'id' => 6,
                'parent_id' => 3,
                'order' => 19,
                'title' => '管理员角色权限管理',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            6 => 
            array (
                'id' => 7,
                'parent_id' => 3,
                'order' => 20,
                'title' => '系统菜单管理',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            7 => 
            array (
                'id' => 8,
                'parent_id' => 3,
                'order' => 23,
                'title' => '系统操作日志',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            8 => 
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
            9 => 
            array (
                'id' => 10,
                'parent_id' => 9,
                'order' => 3,
                'title' => '分类管理',
                'icon' => 'fa-cubes',
                'uri' => 'categories',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            10 => 
            array (
                'id' => 11,
                'parent_id' => 9,
                'order' => 4,
                'title' => '商品管理',
                'icon' => 'fa-file-text',
                'uri' => 'goods',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            11 => 
            array (
                'id' => 12,
                'parent_id' => 3,
                'order' => 21,
                'title' => '资源管理',
                'icon' => 'fa-file',
                'uri' => 'media',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            12 => 
            array (
                'id' => 13,
                'parent_id' => 3,
                'order' => 17,
                'title' => '系统配置管理',
                'icon' => 'fa-toggle-on',
                'uri' => 'config',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            13 => 
            array (
                'id' => 14,
                'parent_id' => 3,
                'order' => 22,
                'title' => '备份管理',
                'icon' => 'fa-copy',
                'uri' => 'backup',
                'permission' => NULL,
                'created_at' => '2017-11-04 11:15:46',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            14 => 
            array (
                'id' => 19,
                'parent_id' => 0,
                'order' => 8,
                'title' => '广告管理',
                'icon' => 'fa-adn',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-01-01 22:53:23',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            15 => 
            array (
                'id' => 20,
                'parent_id' => 19,
                'order' => 9,
                'title' => '轮播管理',
                'icon' => 'fa-caret-square-o-right',
                'uri' => 'carouselAds',
                'permission' => NULL,
                'created_at' => '2019-01-01 22:58:52',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            16 => 
            array (
                'id' => 21,
                'parent_id' => 19,
                'order' => 10,
                'title' => '广播管理',
                'icon' => 'fa-feed',
                'uri' => 'broadcasts',
                'permission' => NULL,
                'created_at' => '2019-08-18 00:09:15',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            17 => 
            array (
                'id' => 22,
                'parent_id' => 9,
                'order' => 5,
                'title' => '运费模板管理',
                'icon' => 'fa-truck',
                'uri' => 'freightTemplates',
                'permission' => NULL,
                'created_at' => '2019-10-08 09:04:11',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            18 => 
            array (
                'id' => 23,
                'parent_id' => 9,
                'order' => 6,
                'title' => '订单管理',
                'icon' => 'fa-file-text-o',
                'uri' => 'orders',
                'permission' => NULL,
                'created_at' => '2019-10-08 09:05:14',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            19 => 
            array (
                'id' => 24,
                'parent_id' => 9,
                'order' => 7,
                'title' => '售后申请管理',
                'icon' => 'fa-file-archive-o',
                'uri' => 'refundOrders',
                'permission' => NULL,
                'created_at' => '2019-10-16 00:00:48',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            20 => 
            array (
                'id' => 25,
                'parent_id' => 19,
                'order' => 11,
                'title' => '资讯管理',
                'icon' => 'fa-bars',
                'uri' => 'articles',
                'permission' => NULL,
                'created_at' => '2019-10-23 00:50:17',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            21 => 
            array (
                'id' => 26,
                'parent_id' => 0,
                'order' => 12,
                'title' => '用户管理',
                'icon' => 'fa-users',
                'uri' => 'users',
                'permission' => NULL,
                'created_at' => '2019-10-26 19:12:27',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            22 => 
            array (
                'id' => 27,
                'parent_id' => 0,
                'order' => 13,
                'title' => '代理管理',
                'icon' => 'fa-user-secret',
                'uri' => NULL,
                'permission' => NULL,
                'created_at' => '2019-10-26 19:57:26',
                'updated_at' => '2019-10-26 19:59:27',
            ),
            23 => 
            array (
                'id' => 28,
                'parent_id' => 27,
                'order' => 14,
                'title' => '代理配置管理',
                'icon' => 'fa-cogs',
                'uri' => 'agencyConfigs',
                'permission' => NULL,
                'created_at' => '2019-10-26 19:59:00',
                'updated_at' => '2019-10-26 19:59:27',
            ),
        ));
        
        
    }
}