<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHasMemberLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_member_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('member_level_id')->default(0);
            $table->json('member_level_snapshot');
            $table->unsignedInteger('member_recharge_order_id')->default(0);
            $table->json('order_snapshot');
            $table->timestamp('validity_started_at')->nullable();
            $table->timestamp('validity_ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_member_levels');
    }
}
