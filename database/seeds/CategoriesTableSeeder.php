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
                'title' => '医用测温',
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
                'title' => '吸发理发',
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
                'title' => '棉柔巾',
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
                'title' => '卫生湿巾',
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
                'title' => '两用棉签',
                'thumbnail' => NULL,
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-08-18 00:40:18',
                'updated_at' => '2019-08-18 00:40:18',
                'deleted_at' => NULL,
            ),
            5 =>
                array (
                    'id' => 6,
                    'title' => '有机洗护',
                    'thumbnail' => NULL,
                    'has_enabled' => 1,
                    'sort' => 0,
                    'created_at' => '2019-08-18 00:40:18',
                    'updated_at' => '2019-08-18 00:40:18',
                    'deleted_at' => NULL,
                ),
            6 =>
                array (
                    'id' => 7,
                    'title' => '驱蚊防晒',
                    'thumbnail' => NULL,
                    'has_enabled' => 1,
                    'sort' => 0,
                    'created_at' => '2019-08-18 00:40:18',
                    'updated_at' => '2019-08-18 00:40:18',
                    'deleted_at' => NULL,
                ),
            7 =>
                array (
                    'id' => 8,
                    'title' => '喂养系列',
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