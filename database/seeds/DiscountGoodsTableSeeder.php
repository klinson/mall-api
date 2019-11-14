<?php

use Illuminate\Database\Seeder;

class DiscountGoodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('discount_goods')->delete();
        
        \DB::table('discount_goods')->insert(array (
            0 => 
            array (
                'id' => 1,
                'goods_id' => 1,
                'goods_specification_id' => 1,
                'title' => '富士山苹果特价大甩卖',
                'price' => 1,
                'quantity' => 9999,
                'sold_quantity' => 10,
                'weight' => '0.2000',
                'has_enabled' => 1,
                'sort' => 0,
                'thumbnail' => NULL,
                'images' => '[]',
                'detail' => '<p>富士山苹果特价大甩卖</p>',
                'tags' => '["超级甜", "富含维生素", "吃了能修仙"]',
                'created_at' => '2019-11-14 15:35:29',
                'updated_at' => '2019-11-14 15:35:31',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'goods_id' => 2,
                'goods_specification_id' => 3,
                'title' => '火龙果特价大甩卖',
                'price' => 2,
                'quantity' => 9999,
                'sold_quantity' => 10,
                'weight' => '0.2000',
                'has_enabled' => 1,
                'sort' => 0,
                'thumbnail' => NULL,
                'images' => '[]',
                'detail' => '<p>火龙果特价大甩卖</p>',
                'tags' => '["超级甜", "富含维生素", "吃了能上天"]',
                'created_at' => '2019-11-14 15:35:29',
                'updated_at' => '2019-11-14 15:35:31',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}