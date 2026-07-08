<?php

use App\Criteria\CategoryCriteria;
use App\Criteria\MediaCriteria;
use App\Criteria\UserCriteria;
use App\Models\Category;
use App\Models\Playlist;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\MediaRepository;
use App\Repositories\Admin\UserRepository;
use Faker\Generator as Faker;

$factory->define(Playlist::class, function (Faker $faker) {
    $userRepository = app()->make(UserRepository::class);
    $artists = $userRepository->pushCriteria(new UserCriteria([]))->pluck('id')->all();

    return [
        'name'         => $faker->firstName,
        'user_id'      => $faker->randomElement($artists),
        'image'        => $faker->randomElement([
            'public/playlist_images/IJYgKDjoqwQnOBi7RTE9i2wmYwVsynsit19GcmUZ.jpeg',
            null
        ]),
        'type'         => $faker->randomElement(array_keys(Category::$TYPES)),
        'is_protected' => $faker->boolean,
    ];
});
$factory->afterCreating(Playlist::class, function ($playlist, Faker $faker) {
    if ($playlist->is_protected) {
        $playlist->is_featured = $faker->boolean;
    }
    $playlist->save();

    $categoryRepository = app()->make(CategoryRepository::class);
    $categories = $categoryRepository->pushCriteria(new CategoryCriteria(['type' => $playlist->type]))->pluck('id')->all();

    $mediaRepository = app()->make(MediaRepository::class);
    $media = $mediaRepository->pushCriteria(new MediaCriteria([
        'category_id' => $categories
    ]))->pluck('id')->all();

    $playlist->media()->attach($faker->randomElements($media, $faker->numberBetween(0, count($media))));
});