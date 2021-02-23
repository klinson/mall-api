<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInfoToCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('limit')->default(1);
            $table->timestamp('draw_started_at')->nullable();
            $table->timestamp('draw_ended_at')->nullable();
            $table->timestamp('valid_started_at')->nullable();
            $table->timestamp('valid_ended_at')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('all_quantity')->default(0);
            $table->unsignedTinyInteger('sort')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('limit');
            $table->dropColumn('draw_started_at');
            $table->dropColumn('draw_ended_at');
            $table->dropColumn('valid_started_at');
            $table->dropColumn('valid_ended_at');
            $table->dropColumn('quantity');
            $table->dropColumn('all_quantity');
            $table->dropColumn('sort');
        });
    }
}
