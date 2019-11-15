<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('goods_id')->default(0);
            $table->unsignedInteger('goods_specification_id')->default(0);
            $table->string('title');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('sold_quantity')->default(0);
            $table->unsignedDecimal('weight', 8, 4)->default(0);
            $table->unsignedTinyInteger('has_enabled')->default(1);
            $table->unsignedTinyInteger('sort')->default(0);
            $table->string('thumbnail')->nullable();
            $table->json('images');
            $table->text('detail')->nullable();
            $table->json('tags');
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
        Schema::dropIfExists('discount_goods');
    }
}
