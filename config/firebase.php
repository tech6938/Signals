<?php

return [

    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS'), // path to service account JSON
    ],

    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),

        'http' => [
            // instead of false, point it to the real cert
            'verify' => 'C:/wamp64/bin/php/php8.2.26/extras/ssl/cacert.pem',
        ],
    ],

];
