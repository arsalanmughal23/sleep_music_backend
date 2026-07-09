<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Analytic;
use Faker\Generator as Faker;

$factory->define(Analytic::class, function (Faker $faker) {

    return [
        'music_id' => $faker->word,
        'views' => $faker->word,
        'user_id' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
