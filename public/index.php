<?php

if (PHP_SAPI == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (is_file(__DIR__ . $url['path'])) {
        return false;
    }
}

define('ROOT_PATH', __DIR__ . '/..');

require ROOT_PATH . "/vendor/autoload.php";

(Dotenv\Dotenv::create(ROOT_PATH))->load();

$app = new \Slim\App(require ROOT_PATH . '/app/configuration.php');

$container = $app->getContainer();
foreach (require ROOT_PATH . '/app/dependencies.php' as $id => $dependency) {
    $container[$id] = $dependency;
}
$container['jexupdate'] = require ROOT_PATH . '/app/jexupdate.php';

require ROOT_PATH . '/app/routes.php';

$app->run();