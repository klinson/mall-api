<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'wxapp_openid' => str_random(28),
        'nickname' => $faker->name,
        'sex' => $faker->randomKey([1, 2]),
        'mobile' => '158'.random_string(8),
        'has_enabled' => 1,
        'avatar' => '',
        'wxapp_userinfo' => "{}",
        'created_at' => $faker->dateTime()->format('Y-m-d H:i:s'),
        'updated_at' => $faker->dateTime()->format('Y-m-d H:i:s'),
    ];
});
