<?php

define('ROOT_PATH', __DIR__.'/..');

require ROOT_PATH."/vendor/autoload.php";

use Dotenv\Dotenv;
use JEXUpdate\Updates\Application\Actions\GenerateExtensionUpdatesCollection;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Infrastructure\Persistence\GitHubExtensionsRepository;
use JEXUpdate\Updates\Infrastructure\Persistence\GitHubUpdateRepository;
use JEXUpdate\Updates\Infrastructure\Serializers\XMLSerializer;
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
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());

    $line = new Monolog\Formatter\LineFormatter();
    $line->allowInlineLineBreaks(true);

    $stream = new Monolog\Handler\StreamHandler($settings['path'], $settings['level']);
    $stream->setFormatter($line);

    $logger->pushHandler($stream);

    return $logger;
};
$container['client'] = function (ContainerInterface $container) {
    return new JEXServer\Service\Github\Client(
        $container->get('github'),
        new GuzzleHttp\Client(),
        $container->get('logger')
    );
};
$container[ExtensionRepository::class] = function (ContainerInterface $container) {
    return new GitHubExtensionsRepository($container->get('github'), new GuzzleHttp\Client());
};
$container[UpdateRepository::class] = function (ContainerInterface $container) {
    return new GitHubUpdateRepository($container->get('github'), new GuzzleHttp\Client());
};
$container[GenerateExtensionUpdatesCollection::class] = function (ContainerInterface $container) {
    return new GenerateExtensionUpdatesCollection(
        $container->get(UpdateRepository::class),
        new XMLSerializer()
    );
};
$app->get('/[{extension}]', '\JEXServer\Controllers\Controller:index');
$app->run();