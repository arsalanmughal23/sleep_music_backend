<?php

use App\Criteria\CategoryCriteria;
use App\Criteria\MediaCriteria;
use App\Criteria\UserCriteria;
use App\Models\Media;
use App\Models\Role;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\MediaRepository;
use App\Repositories\Admin\UserRepository;
use Faker\Generator as Faker;

$factory->define(Media::class, function (Faker $faker) {
    $userRepository = app()->make(UserRepository::class);
    $artists = $userRepository->pushCriteria(new UserCriteria([
        'role' => Role::ROLE_ARTIST
    ]))->pluck('id')->all();

    $categoryRepository = app()->make(CategoryRepository::class);
    //'type' => \App\Models\Category::TYPE_AUDIO
    $categories = $categoryRepository->pushCriteria(new CategoryCriteria([]))->pluck('id')->all();


    return [
        'name'        => $faker->firstName,
//        'user_id'     => $faker->randomElement([4, 5]),
        'user_id'     => $faker->randomElement($artists),
        'category_id' => $faker->randomElement($categories),
        'is_featured' => $faker->boolean,
        'image'       => $faker->randomElement([
            'public/media_images/e1uBNCPtpTyShNDs4B4LXbpYavFoQVqgY1s2nHiC.jpeg',
            'public/media_images/np7VdAaF7q6lvEgidzNFAqPCmxSbXJs7r4d2qWuq.jpeg',
            'public/media_images/e1uBNCPtpTyShNDs4B4LXbpYavFoQVqgY1s2nHiC.jpeg',
        ]),
    ];
});


$factory->afterCreating(Media::class, function ($media, $faker) {

    if ($media->category->type == \App\Models\Category::TYPE_AUDIO) {
        $media->file_path = $faker->randomElement([
            'public/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
            'public/media_files/3HHsZgTyUY8V5AG0QJ5h99QQXbcEiyzNfCvDm9sS.mpga',
            'public/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
        ]);
        $media->file_mime = 'audio/mpeg';
        $media->file_url = $faker->randomElement([
            '/public/storage/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
            '/public/storage/media_files/3HHsZgTyUY8V5AG0QJ5h99QQXbcEiyzNfCvDm9sS.mpga',
            '/public/storage/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
        ]);
    } else {
        $media->file_path = $faker->randomElement([
            'public/media_files/zVydyAKKlAzWcCOL8bf08lgtU3UTcAUtzX2Brjnq.mp4'
        ]);
        $media->file_mime = 'video/mp4';
        $media->file_url = $faker->randomElement([
            '/public/storage/media_files/zVydyAKKlAzWcCOL8bf08lgtU3UTcAUtzX2Brjnq.mp4'
        ]);
    }


    /*$mediaRepository = app()->make(MediaRepository::class);
    $oldMedia = $mediaRepository->pushCriteria(new MediaCriteria([
        'category_id' => $media->category_id
    ]))->all();
    $media->file_path = $faker->randomElement($oldMedia->pluck('file_path'));
    $media->file_mime = $faker->randomElement($oldMedia->pluck('file_mime'));
    $media->file_url = $faker->randomElement($oldMedia->pluck('file_url'));*/

    $media->file_type = $media->category->type;
    $media->save();
});

/*
 * 'file_path' => $faker->randomElement([
            'public/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
            'public/media_files/3HHsZgTyUY8V5AG0QJ5h99QQXbcEiyzNfCvDm9sS.mpga',
            'public/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
        ]),
        'file_type' => 10,
        'file_mime' => 'audio/mpeg',
        'file_url'  => $faker->randomElement([
            '/public/storage/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
            '/public/storage/media_files/3HHsZgTyUY8V5AG0QJ5h99QQXbcEiyzNfCvDm9sS.mpga',
            '/public/storage/media_files/USYHHjwXoyQNOV8D0FpDnMG1yRW3bO5ihyNc4Ge5.mpga',
        ]),
 *
 * */