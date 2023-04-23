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
    'flickr'=> [
        'key' => env('FLICKR_KEY'),
        'secret' => env('FLICKR_SECRET'),
        'callback' => env('FLICKR_CALLBACK'),
    ],
    'queues' => [
        'limit' => 10,
    ]
];
