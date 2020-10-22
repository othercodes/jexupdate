<?php

declare(strict_types=1);

use JEXServer\Actions\GetExtensions;
use JEXServer\Actions\GetExtensionUpdates;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', fn(Request $request, Response $response) => $response);
    $app->get('/', GetExtensions::class);
    $app->get('/{extension}', GetExtensionUpdates::class);
};