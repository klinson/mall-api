<?php

use Illuminate\Database\Seeder;

class AddressesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('addresses')->delete();
        
        \DB::table('addresses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'name' => 'klinson',
                'mobile' => '15818253017',
                'province_code' => 440000,
                'city_code' => 440100,
                'district_code' => 440103,
                'address' => '广州荔湾区1111',
                'is_default' => 0,
                'created_at' => '2019-10-08 22:28:20',
                'updated_at' => '2021-02-27 02:02:56',
                'deleted_at' => NULL,
                'city_name' => '广东省/广州市/荔湾区',
            ),
        ));
        
        
    }
}