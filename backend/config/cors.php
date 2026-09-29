<?php

return [
    'paths' => [],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_origins' => [],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Accept', 'Content-Type', 'X-Correlation-ID', 'Idempotency-Key'],
    'exposed_headers' => ['X-Correlation-ID'],
    'max_age' => 600,
    'supports_credentials' => false,
];
