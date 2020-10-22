<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use GuzzleHttp\Client as HTTP;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Infrastructure\Persistence\GitHubExtensionsRepository;
use JEXUpdate\Shared\Infrastructure\Persistence\GitHubConfiguration;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Infrastructure\Persistence\GitHubUpdateRepository;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            ExtensionRepository::class => function (ContainerInterface $container) {
                $services = $container->get('services');
                $jexServer = $container->get('jexserver');

                return new GitHubExtensionsRepository(
                    new GitHubConfiguration(
                        $services['github'] + ['extensions' => $jexServer['extensions']]
                    ),
                    new HTTP()
                );
            },
            UpdateRepository::class    => function (ContainerInterface $container) {
                $services = $container->get('services');
                $jexServer = $container->get('jexserver');

                return new GitHubUpdateRepository(
                    new GitHubConfiguration(
                        $services['github'] + ['extensions' => $jexServer['extensions']]
                    ),
                    new HTTP()
                );
            },
        ]
    );
};