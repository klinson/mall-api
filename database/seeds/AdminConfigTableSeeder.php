<?php

use Illuminate\Database\Seeder;

class AdminConfigTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admin_config')->delete();
        
        \DB::table('admin_config')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'system.articles.about_us',
                'value' => '1',
                'description' => '关于我们资讯关联ID',
                'created_at' => '2019-10-23 00:52:55',
                'updated_at' => '2019-10-23 00:52:55',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'system.articles.join_us',
                'value' => '2',
                'description' => '入驻我们资讯关联ID',
                'created_at' => '2019-10-23 00:53:25',
                'updated_at' => '2019-10-23 00:53:25',
            ),
        ));
        
        
    }
}