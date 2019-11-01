<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberRechargeActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_recharge_activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('member_level_id')->default(0);
            $table->unsignedTinyInteger('validity_type')->default(0);
            $table->unsignedInteger('validity_times')->default(0);
            $table->unsignedInteger('recharge_threshold')->default(0);
            $table->unsignedTinyInteger('level')->default(0);
            $table->unsignedTinyInteger('invite_award_mode')->default(0);
            $table->unsignedInteger('invite_award')->default(0);
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
        Schema::dropIfExists('member_recharge_activities');
    }
}
