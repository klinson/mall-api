<?php

use Illuminate\Database\Seeder;

class PrizesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('prizes')->delete();
        
        \DB::table('prizes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '湿纸巾',
                'thumbnail' => NULL,
                'origin_quantity' => 20,
                'quantity' => 20,
                'price' => 1000,
                'level' => 1,
                'rate' => 20,
                'has_enabled' => 1,
                'created_at' => '2019-11-03 01:24:17',
                'updated_at' => '2019-11-08 17:12:12',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '额温枪',
                'thumbnail' => NULL,
                'origin_quantity' => 20,
                'quantity' => 20,
                'price' => 9900,
                'level' => 2,
                'rate' => 20,
                'has_enabled' => 1,
                'created_at' => '2019-11-03 01:25:21',
                'updated_at' => '2019-11-08 18:12:55',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '德国奶粉',
                'thumbnail' => NULL,
                'origin_quantity' => 9,
                'quantity' => 9,
                'price' => 28800,
                'level' => 3,
                'rate' => 9,
                'has_enabled' => 1,
                'created_at' => '2019-11-03 01:26:08',
                'updated_at' => '2019-11-08 18:13:11',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => 'iPhone11 Pro',
                'thumbnail' => NULL,
                'origin_quantity' => 1,
                'quantity' => 1,
                'price' => 1288888,
                'level' => 4,
                'rate' => 1,
                'has_enabled' => 1,
                'created_at' => '2019-11-03 01:32:11',
                'updated_at' => '2019-11-03 01:32:16',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}