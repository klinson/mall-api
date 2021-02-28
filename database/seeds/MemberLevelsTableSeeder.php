<?php

use Illuminate\Database\Seeder;

class MemberLevelsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('member_levels')->delete();
        
        \DB::table('member_levels')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '普通会员',
                'logo' => NULL,
                'discount' => 99,
                'has_enabled' => 1,
                'level' => 1,
                'created_at' => '2021-02-27 17:49:58',
                'updated_at' => '2021-02-27 17:50:28',
                'deleted_at' => NULL,
                'is_fee_freight' => 0,
                'score' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '黄金会员',
                'logo' => NULL,
                'discount' => 97,
                'has_enabled' => 1,
                'level' => 2,
                'created_at' => '2019-11-01 12:02:23',
                'updated_at' => '2021-02-27 17:54:25',
                'deleted_at' => NULL,
                'is_fee_freight' => 0,
                'score' => 5000,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '钻石会员',
                'logo' => NULL,
                'discount' => 95,
                'has_enabled' => 1,
                'level' => 3,
                'created_at' => '2019-11-01 12:02:58',
                'updated_at' => '2021-02-27 17:54:49',
                'deleted_at' => NULL,
                'is_fee_freight' => 0,
                'score' => 20000,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => '至尊会员',
                'logo' => NULL,
                'discount' => 88,
                'has_enabled' => 1,
                'level' => 4,
                'created_at' => '2021-02-27 17:52:41',
                'updated_at' => '2021-02-27 17:54:58',
                'deleted_at' => NULL,
                'is_fee_freight' => 1,
                'score' => 100000,
            ),
        ));
        
        
    }
}