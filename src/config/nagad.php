<?php

return [

    'projectPath' => env('APP_URL', 'http://localhost:2020/'),
    'sandbox' => env("NAGAD_API_SENDBOX", "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs"),
    'live' => env("NAGAD_API_LIVE", "https://api.mynagad.com/api/dfs"),

    'apiCredentials' => [
        'merchant_id' => env("NAGAD_MERCHENT_ID", '683002007104225'),
        'wallet' => env("NAGAD_WALLET", '01958095001'),
    ],
    'apiUrl' => [
        'initialize' => "/check-out/initialize/",
        'complete' => "/check-out/complete/",
        'verify' => "/verify/payment/",
        'callback' => "/callback/nagad",
    ],
    'currencyCode' => '050',
    'status' => 'sandbox', //sandbox or live
];
