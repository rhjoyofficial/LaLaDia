<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Meta Conversion API
    |--------------------------------------------------------------------------
    */
    'meta_pixel_id'       => env('META_PIXEL_ID'),
    'meta_access_token'   => env('META_ACCESS_TOKEN'),
    'meta_test_event_code' => env('META_TEST_EVENT_CODE'),

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 Measurement Protocol
    |--------------------------------------------------------------------------
    */
    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID'),
    'ga4_api_secret'     => env('GA4_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Excluded IPs
    | Comma-separated list of IPs that should never trigger conversion events.
    | Example: TRACKING_EXCLUDED_IPS="127.0.0.1,192.168.1.1,10.0.0.0/8"
    |--------------------------------------------------------------------------
    */
    'excluded_ips' => array_filter(
        explode(',', env('TRACKING_EXCLUDED_IPS', '')),
        fn($ip) => $ip !== ''
    ),

];
