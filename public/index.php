<?php

define('ROOT_PATH', __DIR__.'/..');

require ROOT_PATH."/vendor/autoload.php";

use Dotenv\Dotenv;
use GuzzleHttp\Client as HTTP;
use JEXUpdate\Extensions\Application\Actions\GetExtensionCollection;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Infrastructure\Persistence\GitHubExtensionsRepository;
use JEXUpdate\Shared\Infrastructure\Persistence\GitHubConfiguration;
use JEXUpdate\Updates\Application\Actions\GetExtensionUpdatesCollection;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Infrastructure\Persistence\GitHubUpdateRepository;
use Psr\Container\ContainerInterface;

if (PHP_SAPI == 'cli-server') {
    if (is_file(__DIR__.parse_url($_SERVER['REQUEST_URI'])['path'])) {
        return false;
    }
}

$environment = Dotenv::create(ROOT_PATH);
$environment->load();

$app = new Slim\App(require ROOT_PATH.'/app/configuration.php');

$container = $app->getContainer();
$container['logger'] = function (ContainerInterface $container) {
    $settings = $container['settings']['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    $line = new Monolog\Formatter\LineFormatter();
    $line->allowInlineLineBreaks(true);

    $stream = new Monolog\Handler\StreamHandler($settings['path'], $settings['level']);
    $stream->setFormatter($line);

    $logger->pushHandler($stream);

    return $logger;
};
$container[HTTP::class] = function (ContainerInterface $container) {
    return new HTTP();
};
$container[ExtensionRepository::class] = function (ContainerInterface $container) {
    return new GitHubExtensionsRepository(
        new GitHubConfiguration(
            $container['services']['github'] + [
                'extensions' => $container['jexserver']['extensions'],
            ]
        ),
        new HTTP()
    );
};
$container[UpdateRepository::class] = function (ContainerInterface $container) {
    return new GitHubUpdateRepository(
        new GitHubConfiguration(
            $container['services']['github'] + [
                'extensions' => $container['jexserver']['extensions'],
            ]
        ),
        new HTTP()
    );
};
$container[GetExtensionUpdatesCollection::class] = function (ContainerInterface $container) {
    return new GetExtensionUpdatesCollection($container[UpdateRepository::class]);
};
$container[GetExtensionCollection::class] = function (ContainerInterface $container) {
    return new GetExtensionCollection($container[ExtensionRepository::class]);
};

$app->get('/', '\JEXServer\Controllers\Controller:index');
$app->get('/{extension}', '\JEXServer\Controllers\Controller:extension');

$app->run();
