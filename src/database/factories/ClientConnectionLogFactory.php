<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ClientConnectionLog;
use Faker\Generator as Faker;

$factory->define(ClientConnectionLog::class, function (Faker $faker) {

    return [
        'client_id' => $faker->word,
        'status' => $faker->word,
        'seconds_until_next' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
