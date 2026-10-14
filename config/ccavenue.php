<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CCAvenue Merchant Credentials
    |--------------------------------------------------------------------------
    |
    | These credentials are provided by CCAvenue under Settings > API Keys
    | in the Merchant Dashboard.
    |
    */
    'merchant_id' => env('CCAVENUE_MERCHANT_ID', ''),
    'access_code' => env('CCAVENUE_ACCESS_CODE', ''),
    'working_key' => env('CCAVENUE_WORKING_KEY', ''),

    /*
    | Mode: 'mock' (Local Simulator), 'test' (Sandbox), or 'live' (Production)
    */
    'mode' => env('CCAVENUE_MODE', 'mock'),

    /*
    | Endpoints
    */
    'endpoints' => [
        'mock' => 'ccavenue.mock',
        'test' => 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction',
        'live' => 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction',
    ],
];
