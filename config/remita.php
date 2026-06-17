<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Remita Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Remita Payment Gateway integration
    |
    */

    'merchant_id' => env('REMITA_MERCHANT_ID', ''),
    'service_type_id' => env('REMITA_SERVICE_TYPE_ID', ''),
    'api_key' => env('REMITA_API_KEY', ''),
    'public_key' => env('REMITA_PUBLIC_KEY', ''),
    'gateway_url' => env('REMITA_GATEWAY_URL', 'https://demo.remita.net/remita/exapp/api/v1/send/api'),
    'demo_mode' => env('REMITA_DEMO_MODE', true),
    
    'live_url' => 'https://login.remita.net/remita/exapp/api/v1/send/api',
    'demo_url' => 'https://demo.remita.net/remita/exapp/api/v1/send/api',
    
    'check_status_url' => env('REMITA_DEMO_MODE', true) 
        ? 'https://demo.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentstatus'
        : 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentstatus',
    
    'widget_url' => env('REMITA_DEMO_MODE', true)
        ? 'https://demo.remita.net/payment/v1/remita-pay-inline.bundle.js'
        : 'https://login.remita.net/payment/v1/remita-pay-inline.bundle.js',
];
