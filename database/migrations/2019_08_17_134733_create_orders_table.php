<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_number', 25);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('address_id')->default(0);
            $table->unsignedInteger('all_price')->default(0);
            $table->unsignedInteger('goods_price')->default(0);
            $table->unsignedInteger('real_price')->default(0);
            $table->unsignedInteger('coupon_price')->default(0);
            $table->unsignedInteger('freight_price')->default(0);
            $table->string('remarks', 100)->default('');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('orders');
    }
}
