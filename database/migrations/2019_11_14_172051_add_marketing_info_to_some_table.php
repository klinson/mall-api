<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserCouponIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->string('marketing_type')->nullable();
            $table->unsignedInteger('marketing_id')->default(0);
        });
        Schema::table('order_goods', function (Blueprint $table) {
            $table->string('marketing_type')->nullable();
            $table->unsignedInteger('marketing_id')->default(0);
        });
        Schema::table('refund_orders', function (Blueprint $table) {
            $table->string('marketing_type')->nullable();
            $table->unsignedInteger('marketing_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->dropColumn('marketing_type');
            $table->dropColumn('marketing_id');
        });
        Schema::table('order_goods', function (Blueprint $table) {
            $table->dropColumn('marketing_type');
            $table->dropColumn('marketing_id');
        });
        Schema::table('refund_orders', function (Blueprint $table) {
            $table->dropColumn('marketing_type');
            $table->dropColumn('marketing_id');
        });
    }
}
