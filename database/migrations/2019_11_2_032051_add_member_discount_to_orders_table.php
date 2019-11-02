<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMemberDiscountToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('member_discount_price')->default(0);
            $table->unsignedInteger('member_discount')->default(0);
        });

        Schema::table('order_goods', function (Blueprint $table) {
            $table->unsignedInteger('real_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('member_discount_price');
            $table->dropColumn('member_discount');
        });
        Schema::table('order_goods', function (Blueprint $table) {
            $table->dropColumn('real_price');
        });
    }
}
