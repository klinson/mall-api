<?php

use Illuminate\Database\Seeder;

class CarouselAdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carousel_ads')->delete();
        
        \DB::table('carousel_ads')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'index_nav',
                'title' => '首页轮播',
                'has_enabled' => 1,
                'created_at' => '2019-10-06 15:02:20',
                'updated_at' => '2019-10-06 15:02:20',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'index_icon',
                'title' => '首页图标入口',
                'has_enabled' => 1,
                'created_at' => '2021-02-07 17:34:18',
                'updated_at' => '2021-02-07 17:34:18',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}