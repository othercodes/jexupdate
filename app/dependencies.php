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
    'client' => function ($c) {
        $settings = $c->get('github');
        return new GuzzleHttp\Client([
            'base_uri' => $settings['uri'],
            'headers' => [
                'Authorization' => 'token ' . $settings['token']
            ]
        ]);
    },
    'dom' => function ($c) {
        return new DOMDocument('1.0', 'utf-8');
    },
];
