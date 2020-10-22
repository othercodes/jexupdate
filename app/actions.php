<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use JEXServer\Actions\GetExtensions;
use JEXServer\Actions\GetExtensionUpdates;

use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions(
        [
            GetExtensions::class       => autowire(GetExtensions::class),
            GetExtensionUpdates::class => autowire(GetExtensionUpdates::class),
        ]
    );
};