<?php

return [
    'logger' => function (\Psr\Container\ContainerInterface $c) {
        $settings = $c->get('settings')['logger'];
        $logger = new Monolog\Logger($settings['name']);
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());

        $line = new \Monolog\Formatter\LineFormatter();
        $line->allowInlineLineBreaks(true);

        $stream = new Monolog\Handler\StreamHandler($settings['path'], $settings['level']);
        $stream->setFormatter($line);

        $logger->pushHandler($stream);
        return $logger;
    },
    'client' => function (\Psr\Container\ContainerInterface $c) {
        return new \JEXUpdate\Service\Github\Client(
            $c->get('github'),
            new GuzzleHttp\Client(),
            $c->get('logger'));
    }
];
