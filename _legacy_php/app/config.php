<?php
/**
 * Tōramally — configuration for local development.
 */
return [
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'toramally',
        'user' => 'root',
        'pass' => '',
    ],
    // Leave empty to detect automatically or set to local testing URL
    'base_url'    => 'http://localhost:8000',
    // Redirect every visit to HTTPS
    'force_https' => false,
    // 'production' hides error details from visitors. Use 'development' only while testing.
    'env'         => 'development',
    // 64 random characters.
    'app_key'     => 'e8f7c9b3a1d4567890abcdef1234567890abcdef1234567890abcdef12345678',
    'timezone'    => 'Asia/Kolkata',
];
