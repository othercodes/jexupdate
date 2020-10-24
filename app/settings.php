<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            'settings'  => [
                'debug'                  => env('APP_DEBUG', false),
                'displayErrorDetails'    => env("DISPLAY_ERROR_DETAILS", false),
                'logger'                 => [
                    'name'  => env('APP_NAME', 'JEXServer'),
                    'path'  => isset($_ENV['docker'])
                        ? 'php://stdout'
                        : __DIR__.'/../var/logs/app.log',
                    'level' => Logger::DEBUG,
                ],
            ],
            'jexserver' => [
                'name'        => env('JEX_SERVER_NAME', 'JEXServer'),
                'description' => env('JEX_SERVER_DESCRIPTION', 'Joomla Extension Update Server'),
                'extensions'  => explode(',', env('JEX_SERVER_EXTENSIONS', '')),
            ],
            'services'  => [
                'github' => [
                    'uri'     => env('GITHUB_URI', 'https://api.github.com/'),
                    'token'   => env('GITHUB_TOKEN'),
                    'account' => env('GITHUB_ACCOUNT'),
                ],
            ],
        ]
    );
};
