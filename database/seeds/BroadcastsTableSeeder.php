<?php

use Illuminate\Database\Seeder;

class BroadcastsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('broadcasts')->delete();
        
        \DB::table('broadcasts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'content' => '欢迎大家使用本商城~本商城乃测试站点，如有咨询，欢迎大家联系作者~~',
                'has_enabled' => 1,
                'created_at' => '2019-08-18 00:38:14',
                'updated_at' => '2019-08-18 00:38:14',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}