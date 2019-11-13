<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberRechargeActivityHasCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_recharge_activity_has_coupons', function (Blueprint $table) {
            $table->unsignedInteger('activity_id');
            $table->unsignedInteger('coupon_id')->default(0);
            $table->unsignedInteger('count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_recharge_activity_has_coupons');
    }
}
