<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefundOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_number', 25);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('order_id')->default(0);
            $table->unsignedInteger('order_goods_id')->default(0);
            $table->unsignedInteger('goods_id')->default(0);
            $table->unsignedInteger('goods_specification_id')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('real_price')->default(0);
            $table->unsignedInteger('real_refund_cost')->default(0);
            $table->unsignedInteger('real_refund_balance')->default(0);
            $table->unsignedInteger('freight_price')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->string('reason_text')->default('');
            $table->json('reason_images');
            $table->char('refund_order_number', 25)->nullable();
            $table->timestamp('expressed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->unsignedInteger('express_id')->default(0);
            $table->string('express_number')->nullable();
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
        Schema::dropIfExists('refund_orders');
    }
}
