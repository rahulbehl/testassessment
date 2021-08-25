<?php

return [
    'jwt' => [
        'hours' => 172800,
        'temp_hours' => 36000,
        'secret' => env('JWT_SECRET'),
        'issuer' => 'lumen-jwt',
        'hash_code' => ['HS256'],
    ],
];
