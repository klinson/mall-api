<?php

use Illuminate\Database\Seeder;

class MemberRechargeActivityHasCouponsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('member_recharge_activity_has_coupons')->delete();
        
        \DB::table('member_recharge_activity_has_coupons')->insert(array (
            0 => 
            array (
                'activity_id' => 1,
                'coupon_id' => 1,
                'count' => 2,
            ),
            1 => 
            array (
                'activity_id' => 1,
                'coupon_id' => 2,
                'count' => 2,
            ),
            2 => 
            array (
                'activity_id' => 1,
                'coupon_id' => 3,
                'count' => 1,
            ),
            3 => 
            array (
                'activity_id' => 2,
                'coupon_id' => 1,
                'count' => 2,
            ),
            4 => 
            array (
                'activity_id' => 2,
                'coupon_id' => 2,
                'count' => 2,
            ),
            5 => 
            array (
                'activity_id' => 2,
                'coupon_id' => 3,
                'count' => 1,
            ),
        ));
        
        
    }
}