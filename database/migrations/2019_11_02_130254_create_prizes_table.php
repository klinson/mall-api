<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('origin_quantity')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('price')->default(0);
            $table->unsignedTinyInteger('level')->default(0);
            $table->unsignedInteger('rate')->default(0);
            $table->unsignedTinyInteger('has_enabled')->default(0);
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
        Schema::dropIfExists('prizes');
    }
}
