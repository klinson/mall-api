<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInviterIdToSomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopping_carts', function (Blueprint $table) {
            $table->unsignedInteger('inviter_id')->default(0);
        });
        Schema::table('order_goods', function (Blueprint $table) {
            $table->unsignedInteger('inviter_id')->default(0);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('inviter_id')->default(0);
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
            $table->dropColumn('inviter_id');
        });
        Schema::table('order_goods', function (Blueprint $table) {
            $table->dropColumn('inviter_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('inviter_id');
        });
    }
}
