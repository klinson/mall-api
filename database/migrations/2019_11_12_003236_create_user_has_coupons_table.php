<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('coupon_id')->default(0);
            $table->json('coupon_snapshot');
            $table->unsignedInteger('discount_money')->default(0);
            $table->unsignedTinyInteger('has_enabled')->default(0);
            $table->unsignedTinyInteger('status')->default(0);
            $table->string('description')->nullable();
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
        Schema::dropIfExists('user_has_coupons');
    }
}
