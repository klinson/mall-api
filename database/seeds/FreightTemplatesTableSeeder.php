<?php

use Illuminate\Database\Seeder;

class FreightTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('freight_templates')->delete();
        
        \DB::table('freight_templates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '广西',
                'basic_cost' => 1000,
                'pinkage_type' => 0,
                'pinkage_number' => 0,
                'continued_cost' => 1000,
                'has_enabled' => 1,
                'sort' => 0,
                'addresses' => '["450000", "450100", "450200", "450300", "450400", "450500", "450600", "450700", "450800", "450900", "451000", "451100", "451200", "451300", "451400"]',
                'created_at' => '2019-10-08 08:50:37',
                'updated_at' => '2019-10-08 08:50:37',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '广东',
                'basic_cost' => 1000,
                'pinkage_type' => 2,
                'pinkage_number' => 10,
                'continued_cost' => 1000,
                'has_enabled' => 1,
                'sort' => 0,
                'addresses' => '["440000", "440100", "440200", "440300", "440400", "440500", "440600", "440700", "440800", "440900", "441200", "441300", "441400", "441500", "441600", "441700", "441800", "441900", "442000", "442100", "445100", "445200", "445300"]',
                'created_at' => '2019-10-08 08:51:26',
                'updated_at' => '2019-10-08 08:58:38',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}