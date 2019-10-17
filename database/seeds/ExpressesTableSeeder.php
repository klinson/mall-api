<?php

use Illuminate\Database\Seeder;

class ExpressesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('expresses')->delete();
        
        \DB::table('expresses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '顺丰',
                'code' => 'shunfeng',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '申通',
                'code' => 'shentong',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '圆通',
                'code' => 'yuantong',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '中通',
                'code' => 'zhongtong',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '百世汇通',
                'code' => 'huitongkuaidi',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '百世物流',
                'code' => 'baishiwuliu',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '韵达',
                'code' => 'yunda',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => '宅急送',
                'code' => 'zhaijisong',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => '天天',
                'code' => 'tiantian',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => '德邦',
                'code' => 'debangwuliu',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => '国通',
                'code' => 'guotongkuaidi',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => '增益',
                'code' => 'zengyisudi',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => '速尔',
                'code' => 'suer',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => '中铁物流',
                'code' => 'ztky',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => '中铁快运',
                'code' => 'zhongtiewuliu',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => '能达',
                'code' => 'ganzhongnengda',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => '优速',
                'code' => 'youshuwuliu',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => '全峰',
                'code' => 'quanfengkuaidi',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => '京东',
                'code' => 'jd',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            19 => 
            array (
                'id' => 20,
                'name' => '邮政包裹/平邮',
                'code' => 'youzhengguonei',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'EMS',
                'code' => 'ems',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}