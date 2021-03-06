<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCofferWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coffer_withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_number', 25)->nullable();
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedInteger('ip')->default(0);
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coffer_withdrawals');
    }
}
