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
            $table->string('description')->nullable();
            $table->unsignedInteger('publishing_house_id')->default(0);
            $table->unsignedInteger('published_at')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('isbn');
            $table->dropColumn('description');
            $table->dropColumn('publishing_house_id');
            $table->dropColumn('published_at');
        });
    }
}
