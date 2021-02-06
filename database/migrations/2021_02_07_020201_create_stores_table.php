<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->string('address');
            $table->double('latitude')->default(0);
            $table->double('longitude')->default(0);
            $table->point('point');
            $table->char('geohash', 12);
            $table->unsignedTinyInteger('has_enabled')->default(1);
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
        Schema::dropIfExists('stores');
    }
}
