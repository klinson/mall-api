<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberRechargeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_recharge_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number');
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('member_recharge_activity_id')->default(0);
            $table->json('member_recharge_activity_snapshot');
            $table->unsignedInteger('member_level_id')->default(0);
            $table->json('member_level_snapshot');
            $table->timestamp('validity_started_at')->nullable();
            $table->timestamp('validity_ended_at')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedInteger('inviter_id')->default(0);
            $table->timestamp('payed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_recharge_orders');
    }
}
