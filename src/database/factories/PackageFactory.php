<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Package;
use Faker\Generator as Faker;

$factory->define(Package::class, function (Faker $faker) {

    return [
        'name' => $faker->word,
        'price' => $faker->randomDigitNotNull,
        'currency' => $faker->word,
        'product_min_limit' => $faker->word,
        'product_max_limit' => $faker->word,
        'package_id_ios' => $faker->word,
        'package_id_android' => $faker->word,
        'status' => $faker->word,
        'is_default' => $faker->word,
        'created_at' => $faker->date('Y-m-d H:i:s'),
        'updated_at' => $faker->date('Y-m-d H:i:s'),
        'deleted_at' => $faker->date('Y-m-d H:i:s')
    ];
});
