<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coffers', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedInteger('unsettle_balance')->default(0);
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coffers');
    }
}
