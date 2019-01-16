<?php

return [
    'settings' => [
        'displayErrorDetails' => env("DISPLAY_ERROR_DETAILS", false),
        'addContentLengthHeader' => env('ADD_CONTENT_LENGTHHEADER', false),
        'logger' => [
            'name' => 'jexupdate',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
    'github' => [
        'uri' => env('GITHUB_URI', 'https://api.github.com/'),
        'token' => env('GITHUB_TOKEN'),
    ]
];