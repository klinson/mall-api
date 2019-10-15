<?php

use Illuminate\Database\Seeder;

class ExpressesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('expresses')->delete();
        
        \DB::table('expresses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '顺丰',
                'code' => 'shunfeng',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '申通',
                'code' => 'shentong',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2019-10-16 00:18:16',
                'updated_at' => '2019-10-16 00:18:20',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}