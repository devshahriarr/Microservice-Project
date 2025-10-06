<?php
return [
    // সেবা গুলো: key = gateway path segment, value = base upstream URL
    'services' => [
        'auth' => env('AUTH_SERVICE_URL', 'http://localhost:8000'),
        'crud' => env('CRUD_SERVICE_URL', 'http://localhost:8001'),
        // পরবর্তীতে add করো: 'payments' => env('PAYMENTS_SERVICE_URL'),
    ],

    // optional defaults
    'timeout' => env('GATEWAY_TIMEOUT', 5), // seconds for upstream calls
    'auth_cache_ttl' => env('GATEWAY_AUTH_CACHE_TTL', 30),
    'strip_auth_header' => env('GATEWAY_STRIP_AUTH_HEADER', true),
];
