<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMemberDiscountToOfflineOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offline_orders', function (Blueprint $table) {
            $table->unsignedInteger('member_discount_price')->default(0);
            $table->unsignedInteger('member_discount')->default(100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offline_orders', function (Blueprint $table) {
            $table->dropColumn('member_discount_price');
            $table->dropColumn('member_discount');
        });
    }
}
