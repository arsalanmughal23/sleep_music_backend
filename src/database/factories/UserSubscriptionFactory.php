<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserSubscription;
use Faker\Generator as Faker;

$factory->define(UserSubscription::class, function (Faker $faker) {

    return [
        'user_id' => $faker->word,
        'package_id' => $faker->word,
        'reference_key' => $faker->word,
        'platform' => $faker->word,
        'data' => $faker->word,
        'expiry_date' => $faker->word,
        'status' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
