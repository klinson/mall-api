<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookInfoToGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->string('isbn')->nullable();
            $table->string('barcode')->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('press_id')->default(0);
            $table->string('publish_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('isbn');
            $table->dropColumn('barcode');
            $table->dropColumn('description');
            $table->dropColumn('press_id');
            $table->dropColumn('publish_date');
        });
    }
}
