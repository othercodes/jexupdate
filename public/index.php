<?php

if (PHP_SAPI == 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI']);
    if (is_file(__DIR__ . $url['path'])) {
        return false;
    }
}

require __DIR__ . "/../vendor/autoload.php";

$app = new \Slim\App(require __DIR__ . '/../app/configuration.php');

$container = $app->getContainer();
foreach (require __DIR__ . '/../app/dependencies.php' as $id => $dependency) {
    $container[$id] = $dependency;
}

require __DIR__ . '/../app/routes.php';

$app->run();