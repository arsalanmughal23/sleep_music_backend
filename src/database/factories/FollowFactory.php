<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Follow;
use Faker\Generator as Faker;

$factory->define(Follow::class, function (Faker $faker) {

    return [
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'followed_user_id' => $faker->word,
        'followed_by_user_id' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
