<?php

require __DIR__."/../vendor/autoload.php";

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Psr\Log\LoggerInterface;
use JEXServer\Handlers\HttpErrorHandler;
use JEXServer\Handlers\ShutdownHandler;
use JEXServer\ResponseEmitter\ResponseEmitter;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

$environment = Dotenv::createImmutable(__DIR__.'/..');
$environment->load();

$containerBuilder = new ContainerBuilder();
if (!env('APP_DEBUG', false)) {
    $containerBuilder->enableCompilation(__DIR__.'/../var/cache');
}

// Set up settings
$settings = require __DIR__.'/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__.'/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__.'/../app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Instantiate the app
AppFactory::setContainer($container);
$app = AppFactory::create();
$callableResolver = $app->getCallableResolver();

// Register routes
$routes = require __DIR__.'/../app/routes.php';
$routes($app);

// Create Request object from globals
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

// Create Error Handler
$responseFactory = $app->getResponseFactory();
$errorHandler = new HttpErrorHandler(
    $callableResolver,
    $responseFactory,
    $container->get(LoggerInterface::class)
);

// Create Shutdown Handler
register_shutdown_function(
    new ShutdownHandler(
        $request,
        $errorHandler,
        env('DISPLAY_ERROR_DETAILS', false)
    )
);

// Add Routing Middleware
$app->addRoutingMiddleware();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(
    env('DISPLAY_ERROR_DETAILS', false),
    true,
    true
);
$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run App & Emit Response
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($app->handle($request));
