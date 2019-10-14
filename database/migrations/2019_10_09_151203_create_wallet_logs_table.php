<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('data_id')->default(0);
            $table->string('data_type')->nullable();
            $table->unsignedInteger('balance')->default(0);
            $table->unsignedTinyInteger('type')->default(0);
            $table->string('description')->default('');
            $table->unsignedInteger('ip')->default(0);
            $table->timestamp('created_at')->nullable();
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
        Schema::dropIfExists('wallet_logs');
    }
}
