<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('order_type')->default(0);
            $table->string('delivery_type')->nullable();
            $table->unsignedInteger('delivery_id')->default(0);
            $table->json('delivery_snapshot')->nullable();
            $table->unsignedInteger('used_integral')->default(0);
            $table->dropColumn('address_id');
            $table->dropColumn('address_snapshot');
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
            $table->dropColumn('order_type');
            $table->dropColumn('delivery_type');
            $table->dropColumn('delivery_id');
            $table->dropColumn('delivery_snapshot');
            $table->dropColumn('used_integral');
            $table->unsignedInteger('address_id')->default(0);
            $table->json('address_snapshot');
        });
    }
}
