<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotteryRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('prize_id')->default(0);
            $table->json('prize_snapshot');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('chance_id')->default(0);
            $table->unsignedInteger('express_id')->default(0);
            $table->string('express_number')->nullable();
            $table->timestamp('expressed_at')->nullable();
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
        Schema::dropIfExists('lottery_records');
    }
}
