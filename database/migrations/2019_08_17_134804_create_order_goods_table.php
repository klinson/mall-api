<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->default(0);
            $table->unsignedInteger('goods_id')->default(0);
            $table->unsignedInteger('goods_specification_id')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->unsignedTinyInteger('refund_status')->default(0);
            $table->json('snapshot');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_goods');
    }
}
