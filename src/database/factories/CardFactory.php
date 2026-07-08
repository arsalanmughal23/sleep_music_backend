<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Card;
use Faker\Generator as Faker;

$factory->define(Card::class, function (Faker $faker) {

    return [
        'payment_method' => $faker->word,
        'user_id' => $faker->word,
        'last_four' => $faker->word,
        'country' => $faker->word,
        'brand' => $faker->word,
        'exp_year' => $faker->word,
        'is_default' => $faker->word,
        'exp_month' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
