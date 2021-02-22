<?php

use Illuminate\Database\Seeder;

class WalletActivitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('wallet_activities')->delete();
        
        \DB::table('wallet_activities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '充100送5元',
                'threshold' => 10000,
                'present' => 500,
                'has_enabled' => 1,
                'created_at' => '2021-02-22 01:54:41',
                'updated_at' => '2021-02-22 01:54:41',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '充200送15元',
                'threshold' => 20000,
                'present' => 1500,
                'has_enabled' => 1,
                'created_at' => '2021-02-22 01:55:11',
                'updated_at' => '2021-02-22 01:55:34',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'title' => '充500送50元',
                'threshold' => 50000,
                'present' => 5000,
                'has_enabled' => 1,
                'created_at' => '2021-02-22 01:55:54',
                'updated_at' => '2021-02-22 01:55:54',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'title' => '充1000送200元',
                'threshold' => 100000,
                'present' => 20000,
                'has_enabled' => 1,
                'created_at' => '2021-02-22 01:56:12',
                'updated_at' => '2021-02-22 01:56:12',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}