<?php

return [
    'google' => [
        'maps' => [
            'api_key' => 'AIzaSyA4UOekeRHXGtZ2why51dFmG5YJPGyS1vo',
        ]
    ],
    's3' => [
        'token' => env('S3TOKEN'),
        'presignedUrl' => env('AWS_PRESIGNED_URL'),
        'accelerateUrl' => env('AWS_ACCELERATED_URL'),
    ],
    'aws'         => [
        'credentials'                      => [
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY')
            ],
            'region'      => env('AWS_DEFAULT_REGION'),
            'version'     => 'latest',
            'endpoint'    => env('DRM_AWS_ENDPOINT')
        ],
        'media_convert_bucket'             => env('AWS_BUCKET'),
        'media_convert_destination_suffix' => "public/encrypted/",
        'media_convert_destination'        => "s3://" . env('AWS_BUCKET') . "/public/encrypted/",
        'media_convert_role'               => env('DRM_AWS_MEDIA_CONVERT_ROLE'),
        'media_convert_queue'              => env('DRM_AWS_MEDIA_CONVERT_QUEUE'),
    ],
    // 0= Master, 10 = Slave;
    'server_type' => env('SERVER_TYPE', '0'),
    'server_id'   => env('SERVER_ID', '1'),
    'limit'       => 20,
    'aws_url'     => env('AWS_URL'),
    'ffmpeg_bin_path' => env('FFMPEG_BIN_PATH', null),
    'free_trail_days' => env('FREE_TRAIL_DAYS', 14),
    'max_image_size_in_kb' => 1024 * 2,
    'max_audio_size_in_kb' => 1024 * 30,
    'each_category_sounds_max_limit' => 5,
    'default_mixer_audio_length' => 5,
    'min_mixer_audio_length' => 1,
    'max_mixer_audio_length' => 30,
];