<?php

return [
    'settings' => [
        'displayErrorDetails' => env("DISPLAY_ERROR_DETAILS", false),
        'addContentLengthHeader' => env('ADD_CONTENT_LENGTHHEADER', false),
        'logger' => [
            'name' => 'jexupdate',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => Monolog\Logger::DEBUG,
        ],
    ],
    'jexupdate' => [
        'server' => [
            'name' => 'otherCode Extensions',
            'description' => 'otherCode Extensions Set'
        ],
        'cache' => 900,
        'repositories' => [
            'mod_simplecontactform' => 'othercodes',
            'tpl_g5_othercode' => 'othercodes',
        ],
    ],
    'github' => [
        'uri' => env('GITHUB_URI', 'https://api.github.com/'),
        'token' => env('GITHUB_TOKEN'),
        'account' => 'othercodes'
    ]
];
