<?php

use Illuminate\Database\Seeder;

class GoodsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('goods')->delete();
        
        \DB::table('goods')->insert(array (
            0 => 
            array (
                'id' => 1,
                'category_id' => 1,
                'title' => '富士山苹果',
                'thumbnail' => NULL,
                'images' => '[]',
                'detail' => '<p style="text-align: center;">富士山苹果极好！</p><p style="text-align: center;"><span style="text-align: center;">富士山苹果极好！</span></p><p style="text-align: center;"><span style="text-align: center;"><span style="text-align: center;">富士山苹果极好！</span></span></p>',
                'max_price' => 1500,
                'min_price' => 500,
                'has_enabled' => 1,
                'has_recommended' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:45:25',
                'updated_at' => '2019-08-18 00:46:39',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'category_id' => 1,
                'title' => '火龙果',
                'thumbnail' => NULL,
                'images' => '[]',
                'detail' => '<p>火龙果考虑一下</p>',
                'max_price' => 2000,
                'min_price' => 100,
                'has_enabled' => 1,
                'has_recommended' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:46:17',
                'updated_at' => '2019-08-18 00:46:17',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}