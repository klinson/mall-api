<?php

use Illuminate\Database\Seeder;

class CouponsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('coupons')->delete();
        
        \DB::table('coupons')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '满100减10元',
                'start_price' => 10000,
                'face_value' => 1000,
                'type' => 2,
                'has_enabled' => 1,
                'created_at' => '2019-11-12 01:27:07',
                'updated_at' => '2019-11-12 01:27:11',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '满150减15元',
                'start_price' => 15000,
                'face_value' => 1500,
                'type' => 2,
                'has_enabled' => 1,
                'created_at' => '2019-11-12 01:27:38',
                'updated_at' => '2019-11-12 01:27:42',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '1折券',
                'start_price' => 10,
                'face_value' => 10,
                'type' => 1,
                'has_enabled' => 1,
                'created_at' => '2019-11-12 01:29:03',
                'updated_at' => '2019-11-12 01:29:07',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}