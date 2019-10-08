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
            $table->char('cancel_order_number', 25)->nullable();
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('address_id')->default(0);
            $table->unsignedInteger('all_price')->default(0);
            $table->unsignedInteger('goods_price')->default(0);
            $table->unsignedInteger('real_price')->default(0);
            $table->unsignedInteger('coupon_price')->default(0);
            $table->unsignedInteger('freight_price')->default(0);
            $table->unsignedInteger('used_balance')->default(0);
            $table->unsignedInteger('real_cost')->default(0);
            $table->string('remarks', 100)->default('');
            $table->tinyInteger('status')->default(0);
            $table->unsignedInteger('freight_template_id')->default(0);
            $table->unsignedTinyInteger('goods_count')->default(0);
            $table->unsignedDecimal('goods_weight', 8, 4)->default(0);
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
        Schema::dropIfExists('orders');
    }
}
