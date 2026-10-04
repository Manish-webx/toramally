<?php
/**
 * Tōramally — configuration.
 *
 * 1. Copy this file to config.php (same folder).
 * 2. Fill in the database details your hosting panel gives you.
 * 3. Paste a long random APP_KEY (the setup guide shows how) and never change it
 *    after launch: it encrypts payment and courier credentials.
 *
 * Everything else (contact details, currencies, GSTIN, gateways, couriers,
 * email) is edited in the admin panel, not here.
 */
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'your_database_name',
        'user' => 'your_database_user',
        'pass' => 'your_database_password',
    ],
    // Leave empty to detect automatically. Set to e.g. 'https://toramally.com' once live.
    'base_url'    => '',
    // Redirect every visit to HTTPS. Turn on once SSL is active on your domain.
    'force_https' => false,
    // 'production' hides error details from visitors. Use 'development' only while testing.
    'env'         => 'production',
    // 64 random characters. See SETUP-GUIDE, step 3.
    'app_key'     => 'CHANGE-ME-TO-64-RANDOM-CHARACTERS',
    'timezone'    => 'Asia/Kolkata',
];
