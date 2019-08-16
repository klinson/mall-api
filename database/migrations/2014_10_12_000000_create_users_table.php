<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->char('wxapp_openid', 28)->comment('账号');
            $table->string('nickname', 30)->comment('昵称');
            $table->tinyInteger('sex')->default(0)->comment('性别: 1-男, 2-女');
            $table->char('mobile', 11)->nullable()->comment('手机');
            $table->tinyInteger('has_enabled')->default(1)->comment('是否启用');

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
