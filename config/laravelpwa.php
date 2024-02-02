<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('APP_NAME', 'My PWA App'),
        'short_name' => 'PWA',
        'lang' => 'pt-BR',
        'start_url' => env('APP_URL'),
        'scope' => '.',
        'display' => 'standalone',
        'background_color' => '#000000',
        'theme_color' => '#000000',
        'orientation'=> 'portrait',
        'status_bar'=> '#000000',
        'prefer_related_applications' => false,
        'dir' => 'ltr',
        'icons' => [
            [
                'src' => '/icons/icon-72x72.png',
                "type"=> "image/png",
                "sizes"=> "72x72",
                'purpose' => 'maskable'
            ],
            [
                'src' => '/icons/icon-96x96.png',
                "type"=> "image/png",
                "sizes"=> "96x96",
                'purpose' => 'maskable'
            ],
            [
                'src' => '/icons/icon-128x128.png',
                "type"=> "image/png",
                "sizes"=> "128x128",
                'purpose' => 'maskable'
            ],
            [
                'src' => '/icons/icon-144x144.png',
                "type"=> "image/png",
                "sizes"=> "144x144",
                'purpose' => 'any'
            ],
            [
                'src' => '/icons/icon-152x152.png',
                "type"=> "image/png",
                "sizes"=> "152x152",
                'purpose' => 'any'
            ],
            [
                'src' => '/icons/icon-192x192.png',
                "type"=> "image/png",
                "sizes"=> "192x192",
                'purpose' => 'maskable'
            ],
            [
                'src' => '/icons/icon-196x196.png',
                "type"=> "image/png",
                "sizes"=> "196x196",
                'purpose' => 'maskable'
            ],
            [
                'src' => '/icons/icon-384x384.png',
                "type"=> "image/png",
                "sizes"=> "384x384",
                'purpose' => 'maskable'
            ],
            [
                'src' => '/icons/icon-512x512.png',
                "type"=> "image/png",
                "sizes"=> "512x512",
                'purpose' => 'maskable'
            ],
        ],
        'splash' => [
            '1125x2436' => '/icons/splash-1125x2436.png',
            '750x1334' => '/icons/splash-750x1334.png',
            '1242x2208' => '/icons/splash-1242x2208.png',
            '640x1136' => '/icons/splash-640x1136.png',
            '1536x2048' => '/icons/splash-1536x2048.png',
            '1668x2224' => '/icons/splash-1668x2224.png',
            '2048x2732' => '/icons/splash-2048x2732.png'
        ],
        'shortcuts' => [
            [
                'name' => 'Shortcut Link 1',
                'description' => 'Shortcut Link 1 Description',
                'url' => '/shortcutlink1',
                'icons' => [
                    "src" => "/icons/icon-72x72.png",
                    "purpose" => "any maskable"
                ]
            ],
            [
                'name' => 'Shortcut Link 2',
                'description' => 'Shortcut Link 2 Description',
                'url' => '/shortcutlink2'
            ]
        ],
        'custom' => []
    ]
];
