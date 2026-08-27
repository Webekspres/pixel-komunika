<?php

return [

    'name' => env('STORE_NAME', 'Pixel Komunika'),
    'address' => env('STORE_ADDRESS', 'Jl. Sawahkurung IV No. 18B, Bandung'),
    'phone' => env('STORE_PHONE', '081546407702'),
    'npwp' => env('STORE_NPWP', '0821.4146.0442.4000'),
    'company_name' => env('STORE_COMPANY_NAME', 'Pixel Komunika'),

    // ponytail: OPN-006 rounding / multi-rate still provisional
    'pph22' => [
        'divisor' => (float) env('PPH22_DIVISOR', 1.11),
        'rounding' => env('PPH22_ROUNDING', 'half_up'), // half_up|floor|ceil
        'multi_category_rate_strategy' => env('PPH22_MULTI_RATE', 'max'), // max|first
    ],

    // ponytail: OPN-022 reseller account format provisional
    'reseller_account_prefix' => env('RESELLER_ACCOUNT_PREFIX', 'PKR'),

    'whatsapp' => [
        'admin_order_phone' => env('WA_ADMIN_ORDER_PHONE', '081546407702'),
        'new_order_message' => env('WA_NEW_ORDER_MESSAGE', 'Cek Order masuk'),
        'driver' => env('WA_DRIVER', 'log'), // log|fake
    ],

    'payment_proof_retain_years' => (int) env('PAYMENT_PROOF_RETAIN_YEARS', 5),

    'fulfillment' => [
        'auto_complete_workdays' => (int) env('ORDER_AUTO_COMPLETE_WORKDAYS', 5),
    ],

    'shipping' => [
        'driver' => env('SHIPPING_DRIVER', 'mock'), // mock|biteship
        // RULE-030: store courier free when subtotal + pph22 >= threshold
        'free_store_courier_threshold' => (int) env('STORE_FREE_SHIPPING_THRESHOLD', 1_000_000),
    ],

    'holidays' => [], // YYYY-MM-DD list; ponytail: replace with calendar when OPN-020 lands

];
