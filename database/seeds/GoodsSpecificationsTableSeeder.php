<?php

use Illuminate\Database\Seeder;

class GoodsSpecificationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('goods_specifications')->delete();
        
        \DB::table('goods_specifications')->insert(array (
            0 => 
            array (
                'id' => 1,
                'goods_id' => 1,
                'title' => '1斤',
                'thumbnail' => NULL,
                'price' => 1000,
                'quantity' => 999,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-20 15:44:28',
                'updated_at' => '2019-08-20 15:44:28',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'goods_id' => 1,
                'title' => '1箱',
                'thumbnail' => NULL,
                'price' => 5000,
                'quantity' => 999,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-20 15:44:28',
                'updated_at' => '2019-08-20 15:44:28',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'goods_id' => 2,
                'title' => '1斤',
                'thumbnail' => NULL,
                'price' => 1000,
                'quantity' => 999,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-20 15:44:28',
                'updated_at' => '2019-08-20 15:44:28',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'goods_id' => 2,
                'title' => '1箱',
                'thumbnail' => NULL,
                'price' => 5000,
                'quantity' => 999,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-20 15:44:28',
                'updated_at' => '2019-08-20 15:44:28',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}