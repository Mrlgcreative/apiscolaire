<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:3000',
        'http://127.0.0.1:8000',
        'https://api.imasomo.com',
        'https://imasomo.com',
        'https://www.imasomo.com',
    ],

    'allowed_origins_patterns' => [
        // Autorise aussi les previews Vercel / autres sous-domaines si besoin
        // '#^https://.*\.vercel\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
