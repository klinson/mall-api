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
                'title' => '黄金会员',
                'logo' => NULL,
                'discount' => 88,
                'has_enabled' => 1,
                'level' => 1,
                'created_at' => '2019-11-01 12:02:23',
                'updated_at' => '2019-11-01 12:02:26',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '钻石会员',
                'logo' => NULL,
                'discount' => 75,
                'has_enabled' => 1,
                'level' => 2,
                'created_at' => '2019-11-01 12:02:58',
                'updated_at' => '2019-11-01 12:03:01',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}