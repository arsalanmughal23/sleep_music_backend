<?php

return [
    'gcm' => [
        'priority' => 'normal',
        'dry_run'  => false,
        'apiKey'   => 'My_ApiKey',
    ],
    'fcm' => [
        'priority' => 'normal',
        'dry_run'  => false,
        'apiKey'   => env('FIREBASE_SERVER_KEY'),
    ],
    'apn' => [
        'certificate' => __DIR__ . '/iosCertificates/apns-dev-cert.pem',
        'passPhrase'  => '1234', //Optional
        'passFile'    => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
        'dry_run'     => true
    ]
];