<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000'], // frontend React bạn đang chạy

    'allowed_headers' => ['*'],

    'supports_credentials' => true,
];
