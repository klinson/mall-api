<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_number', 25);
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('admin_id')->default(0);
            $table->unsignedInteger('all_price')->default(0);
            $table->string('remarks')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('group_orders');
    }
}
