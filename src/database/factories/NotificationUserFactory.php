<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\NotificationUser;
use Faker\Generator as Faker;

$factory->define(NotificationUser::class, function (Faker $faker) {

    return [
        'notification_id' => $faker->word,
        'user_id' => $faker->word,
        'status' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
