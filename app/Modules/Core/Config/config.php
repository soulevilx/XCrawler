<?php

return [
    'name' => 'Core',
    'crawling' => [
        'cache' => [
            'enable' => true,
            'interval' => 60,
        ],
    ],
    'oauth' => [
        'storage' => 'memory',
    ],
    'flickr' => [
        'key' => env('FLICKR_KEY'),
        'secret' => env('FLICKR_SECRET'),
        'callback' => env('FLICKR_CALLBACK'),
    ],
    'pool' => [
        'limit' => env('POOL_LIMIT', 5)
    ]
];
