<?php

use Illuminate\Database\Seeder;

class AgencyConfigsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('agency_configs')->delete();
        
        \DB::table('agency_configs')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '高级代理',
                'recharge_threshold' => 999900,
                'direct_profit_mode' => 2,
                'direct_profit' => 30,
                'indirect_profit_mode' => 1,
                'indirect_profit' => 200,
                'direct_agency_mode' => 1,
                'direct_agency' => 100000,
                'indirect_agency_mode' => 1,
                'indirect_agency' => 50000,
                'created_at' => '2019-10-09 23:50:29',
                'updated_at' => '2019-10-09 23:50:33',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '普通代理',
                'recharge_threshold' => 99900,
                'direct_profit_mode' => 2,
                'direct_profit' => 20,
                'indirect_profit_mode' => 1,
                'indirect_profit' => 200,
                'direct_agency_mode' => 1,
                'direct_agency' => 10000,
                'indirect_agency_mode' => 1,
                'indirect_agency' => 5000,
                'created_at' => '2019-10-09 23:52:09',
                'updated_at' => '2019-10-09 23:52:12',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}