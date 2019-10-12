<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRechargeThresholdOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharge_threshold_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_number', 25);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('agency_config_id')->default(0);
            $table->unsignedInteger('balance')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('recharge_threshold_orders');
    }
}
