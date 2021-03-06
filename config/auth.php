<?php
return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class,
        ],
    ],
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
        'reset' => [
            'expire' => 60,
        ],
    ],
    'password_timeout' => 10800,
    'user' => [
        'access' => [
            'expire' => 1440,
        ]
    ]
];
