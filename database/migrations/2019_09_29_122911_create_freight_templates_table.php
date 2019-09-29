<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreightTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('basic_cost')->default(0);
            $table->unsignedTinyInteger('pinkage_type')->default(0);
            $table->unsignedInteger('pinkage_number')->default(0);
            $table->unsignedTinyInteger('continued_type')->default(0);
            $table->unsignedInteger('continued_cost')->default(0);
            $table->unsignedTinyInteger('has_enabled')->default(0);
            $table->unsignedInteger('sort')->default(0);
            $table->json('addresses');
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
        Schema::dropIfExists('freight_templates');
    }
}
