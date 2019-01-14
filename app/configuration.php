<?php

return [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'logger' => [
            'name' => 'jexupdate',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
    'github' => [
        'uri' => 'https://api.github.com/',
        'token' => '',
    ],
    'service' => [
        'extension' => [
            'types' => [
                'com' => 'component',
                'plg' => 'plugin',
                'mod' => 'module',
            ]
        ],
        'repositories' => [
            'mod_simplecontactform' => 'othercodes',
        ],
    ]
];