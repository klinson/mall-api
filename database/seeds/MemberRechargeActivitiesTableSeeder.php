<?php

use Illuminate\Database\Seeder;

class MemberRechargeActivitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('member_recharge_activities')->delete();
        
        \DB::table('member_recharge_activities')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => '充99得黄金会员享8.8折购物优惠',
                'member_level_id' => 1,
                'validity_type' => 3,
                'validity_times' => 1,
                'recharge_threshold' => 9900,
                'level' => 1,
                'invite_award_mode' => 1,
                'invite_award' => 1980,
                'has_enabled' => 1,
                'created_at' => '2019-11-01 12:04:41',
                'updated_at' => '2019-11-01 12:04:44',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => '充499得永久钻石会员享7.5折购物优惠',
                'member_level_id' => 2,
                'validity_type' => 4,
                'validity_times' => 0,
                'recharge_threshold' => 49900,
                'level' => 2,
                'invite_award_mode' => 1,
                'invite_award' => 9980,
                'has_enabled' => 1,
                'created_at' => '2019-11-01 12:06:13',
                'updated_at' => '2019-11-01 12:06:14',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}