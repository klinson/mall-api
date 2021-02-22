<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToRechargeThresholdOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recharge_threshold_orders', function (Blueprint $table) {
            $table->unsignedInteger('result')->default(0);
            $table->unsignedInteger('wallet_activity_id')->default(0);
            $table->json('wallet_activity_snapshot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recharge_threshold_orders', function (Blueprint $table) {
            $table->dropColumn('result');
            $table->dropColumn('wallet_activity_id');
            $table->dropColumn('wallet_activity_snapshot');
        });
    }
}
