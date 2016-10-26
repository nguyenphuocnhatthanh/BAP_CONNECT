<?php
return [
    'url'           => env('PLATFORM_URL', 'default_url'),
    'grant_type'    => env('PLATFORM_GRANT', 'default_grant'),
    'refresh_token' => '',
    'client_id'     => env('PLATFORM_CLIENT_ID', 'default_id'),
    'client_secret' => env('PLATFORM_CLIENT_SECRET', 'default_id'),
    'scope'         => env('PLATFORM_SCOPE', ''),
    'request'       => [
        'timeout'   => 60 // Set timeout 60s
    ],
    'devices'       => [
        'model'     => \Bap\ConnectPlatform\Models\Device::class,
        'in'        => 'pc|mobile'
    ]
];