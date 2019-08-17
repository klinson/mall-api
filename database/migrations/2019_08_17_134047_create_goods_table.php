<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->default(0);
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->json('images');
            $table->text('detail')->nullable();
            $table->unsignedInteger('max_price')->default(0);
            $table->unsignedInteger('min_price')->default(0);
            $table->unsignedTinyInteger('has_enabled')->default(1);
            $table->unsignedTinyInteger('has_recommended')->default(1);
            $table->unsignedTinyInteger('sort')->default(0);
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
        Schema::dropIfExists('goods');
    }
}
