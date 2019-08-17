<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('categories')->delete();
        
        \DB::table('categories')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '应季水果',
                'thumbnail' => NULL,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:39:29',
                'updated_at' => '2019-08-18 00:40:50',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '馋嘴零食',
                'thumbnail' => NULL,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:39:39',
                'updated_at' => '2019-08-18 00:39:39',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '酒水饮品',
                'thumbnail' => NULL,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:39:59',
                'updated_at' => '2019-08-18 00:39:59',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => '家居百货',
                'thumbnail' => NULL,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:40:12',
                'updated_at' => '2019-08-18 00:40:12',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'title' => '蛋糕甜品',
                'thumbnail' => NULL,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:40:18',
                'updated_at' => '2019-08-18 00:40:18',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}