<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 生成数据集合
        factory(User::class)->times(20)->create();

        // 单独处理几个用户的数据
        $user = User::find(1);
        $user->nickname = 'klinson';
        $user->wxapp_openid = 'klinson';
        $user->mobile = '15818253017';
        $user->sex = 1;
        $user->save();
    }
}
