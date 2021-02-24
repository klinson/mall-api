<?php

use Illuminate\Database\Seeder;

class AuthorsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('authors')->delete();
        
        \DB::table('authors')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '王世光',
                'created_at' => '2021-02-24 23:35:56',
                'updated_at' => '2021-02-24 23:35:56',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '张天宝',
                'created_at' => '2021-02-24 23:36:07',
                'updated_at' => '2021-02-24 23:36:07',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '任海宾',
                'created_at' => '2021-02-24 23:36:15',
                'updated_at' => '2021-02-24 23:36:15',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '陈先云',
                'created_at' => '2021-02-24 23:36:24',
                'updated_at' => '2021-02-24 23:36:24',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '崔峦',
                'created_at' => '2021-02-24 23:36:32',
                'updated_at' => '2021-02-24 23:36:32',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '孟令全',
                'created_at' => '2021-02-24 23:36:37',
                'updated_at' => '2021-02-24 23:36:37',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '老舍',
                'created_at' => '2021-02-24 23:38:24',
                'updated_at' => '2021-02-24 23:38:24',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => '鲁迅',
                'created_at' => '2021-02-24 23:38:33',
                'updated_at' => '2021-02-24 23:38:33',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => '朱自清',
                'created_at' => '2021-02-24 23:38:39',
                'updated_at' => '2021-02-24 23:38:39',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}