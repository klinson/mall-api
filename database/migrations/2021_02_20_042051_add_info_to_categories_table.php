<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('parent_id')->default(0);
            $table->string('full_title')->nullable();
            $table->unsignedTinyInteger('is_recommended')->default(0);
            $table->unsignedInteger('code')->nullable();
            $table->json('search_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->dropColumn('full_title');
            $table->dropColumn('is_recommended');
            $table->dropColumn('code');
            $table->dropColumn('search_ids');
        });
    }
}
