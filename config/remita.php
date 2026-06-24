<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Remita Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file configures the Remita Payment Gateway integration used for
    | collecting school fees, admission payments, and other charges.
    |
    | HOW TO CONFIGURE:
    | -----------------
    | 1. Sign up at https://login.remita.net (Live) or https://demo.remita.net (Demo)
    | 2. After approval, you will receive:
    |    - Merchant ID: Your unique Remita merchant identifier
    |    - Service Type ID: The service type for receiving payments
    |    - API Key: Secret key for server-to-server communication
    |    - Public Key: Used for client-side payment widget initialization
    |
    | 3. Add these values to your .env file:
    |    REMITA_MERCHANT_ID=your_merchant_id
    |    REMITA_SERVICE_TYPE_ID=your_service_type_id
    |    REMITA_API_KEY=your_api_key
    |    REMITA_PUBLIC_KEY=your_public_key
    |    REMITA_DEMO_MODE=true  (set to false for live/production)
    |
    | DEMO/TEST CREDENTIALS (for testing):
    | ------------------------------------
    |    REMITA_MERCHANT_ID=2547916
    |    REMITA_SERVICE_TYPE_ID=4430731
    |    REMITA_API_KEY=1946
    |    REMITA_PUBLIC_KEY=QzAwMDAxOTQ0NjZ8MTEwNjE4NjF8OWZjOWI4NTBlMGVlNGQ5OTkzYTBiYTBiMDhlMmQ5MTI
    |    REMITA_DEMO_MODE=true
    |
    | NOTE: When going live, set REMITA_DEMO_MODE=false and replace the
    |       demo credentials above with your actual production credentials.
    |
    */

    // Your Remita Merchant ID (provided during registration)
    'merchant_id' => env('REMITA_MERCHANT_ID', ''),

    // Service Type ID for payment collection
    'service_type_id' => env('REMITA_SERVICE_TYPE_ID', ''),

    // API Key for server-side hash generation and verification
    'api_key' => env('REMITA_API_KEY', ''),

    // Public Key for client-side widget initialization
    'public_key' => env('REMITA_PUBLIC_KEY', ''),

    // Gateway URL for RRR generation (auto-selected based on demo_mode)
    'gateway_url' => env('REMITA_GATEWAY_URL', 'https://demo.remita.net/remita/exapp/api/v1/send/api'),

    // Set to true for demo/testing, false for live/production
    'demo_mode' => env('REMITA_DEMO_MODE', true),

    // Pre-configured URLs (do not change unless Remita updates their API)
    'live_url' => 'https://login.remita.net/remita/exapp/api/v1/send/api',
    'demo_url' => 'https://demo.remita.net/remita/exapp/api/v1/send/api',

    'check_status_url' => env('REMITA_DEMO_MODE', true) 
        ? 'https://demo.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentstatus'
        : 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentstatus',

    // Remita Inline Payment Widget JS (loaded on payment pages)
    'widget_url' => env('REMITA_DEMO_MODE', true)
        ? 'https://demo.remita.net/payment/v1/remita-pay-inline.bundle.js'
        : 'https://login.remita.net/payment/v1/remita-pay-inline.bundle.js',
];
