<?php

define('ROOT_PATH', __DIR__ . '/..');

require ROOT_PATH . "/vendor/autoload.php";

if (PHP_SAPI == 'cli-server') {
    if (is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'])['path'])) {
        return false;
    }
}

$environment = Dotenv\Dotenv::create(ROOT_PATH);
$environment->load();

$app = new Slim\App(require ROOT_PATH . '/app/configuration.php');

$container = $app->getContainer();
$container['logger'] = function (Psr\Container\ContainerInterface $container) {
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
$container['client'] = function (Psr\Container\ContainerInterface $container) {
    return new JEXServer\Service\Github\Client(
        $container->get('github'),
        new GuzzleHttp\Client(),
        $container->get('logger'));
};

$app->get('/[{extension}]', '\JEXServer\Controllers\Controller:index');
$app->run();