<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgencyConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('recharge_threshold')->default(0);
            $table->unsignedTinyInteger('direct_profit_mode')->default(0);
            $table->unsignedInteger('direct_profit')->default(0);
            $table->unsignedTinyInteger('indirect_profit_mode')->default(0);
            $table->unsignedInteger('indirect_profit')->default(0);
            $table->unsignedTinyInteger('direct_agency_mode')->default(0);
            $table->unsignedInteger('direct_agency')->default(0);
            $table->unsignedTinyInteger('indirect_agency_mode')->default(0);
            $table->unsignedInteger('indirect_agency')->default(0);
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
        Schema::dropIfExists('agency_configs');
    }
}
