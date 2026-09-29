<?php

return [
    'malware_scanning' => [
        'enabled' => (bool) env('MALWARE_SCAN_ENABLED', false),
        'binary' => env('MALWARE_SCAN_BINARY', 'clamdscan'),
        'timeout' => (int) env('MALWARE_SCAN_TIMEOUT', 30),
    ],
];
