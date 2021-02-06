<?php

use Illuminate\Database\Seeder;

class StoresTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('stores')->delete();
        
        \DB::table('stores')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '昇华书店长安店',
                'thumbnail' => 'images/5aa3d97eab2700b635e7676da780e266.png',
                'address' => '广东省东莞市长安镇莲峰路21号东莞市长安中学',
                'latitude' => 22.804261,
                'longitude' => 113.806707,
                'point' => \DB::raw('ST_GeomFromText ("POINT(113.807 22.8043)")'),
                'geohash' => 'ws0ctxrs2qr7',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2021-02-07 03:10:22',
                'updated_at' => '2021-02-07 03:10:49',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '昇华书店茶山店',
                'thumbnail' => 'images/7f9c7832c861950bd3557454633ffbd2.png',
                'address' => '广东省东莞市茶山镇彩虹路111号',
                'latitude' => 23.070829,
                'longitude' => 113.869435,
                'point' => \DB::raw('ST_GeomFromText ("POINT(113.869 23.0708)")'),
                'geohash' => 'ws0gppvjq4hx',
                'has_enabled' => 1,
                'sort' => 0,
                'created_at' => '2021-02-07 03:11:48',
                'updated_at' => '2021-02-07 03:11:48',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}