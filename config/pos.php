<?php

return [

    'driver' => env('POS_DRIVER', 'sample'),

    'base_url' => env('POS_BASE_URL'),

    'static_api_key' => env('POS_STATIC_API_KEY'),

    'timeout' => (int) env('POS_TIMEOUT', 10),

];
