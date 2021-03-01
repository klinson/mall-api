<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfflineOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_number', 25);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('staff_id')->default(0);
            $table->unsignedInteger('store_id')->default(0);
            $table->unsignedInteger('all_price')->default(0);
            $table->unsignedInteger('used_integral')->default(0);
            $table->unsignedInteger('real_price')->default(0);
            $table->unsignedInteger('used_balance')->default(0);
            $table->unsignedInteger('real_cost')->default(0);
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamp('confirmed_at')->nullable();
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
        Schema::dropIfExists('offline_orders');
    }
}
