<?php

declare(strict_types=1);

namespace JEXUpdate\Tests\Unit;

use Faker\Factory;
use JEXUpdate\Updates\Application\Contracts\UpdateSource;
use JEXUpdate\Updates\Application\UpdateAssembler;
use JEXUpdate\Updates\Domain\Exceptions\UpdateAssemblingFailure;
use JEXUpdate\Updates\Domain\Update;

/**
 * Create a new UpdateSource object.
 *
 * @param  array  $override
 * @param  callable|null  $source
 *
 * @return UpdateSource
 */
function makeUpdateSource(array $override = [], ?callable $source = null): UpdateSource
{
    if (is_null($source)) {
        $source = function (array $properties): UpdateSource {
            return new class($properties) implements UpdateSource {
                protected array $properties;

                public function __construct(array $properties)
                {
                    $this->properties = $properties;
                }

                public function name(): string
                {
                    return $this->properties['name'];
                }

                public function element(): string
                {
                    return $this->properties['element'];
                }

                public function type(): string
                {
                    return $this->properties['type'];
                }

                public function version(): string
                {
                    return $this->properties['version'];
                }

                public function description(): string
                {
                    return $this->properties['description'];
                }

                public function infoURL(): string
                {
                    return $this->properties['infoURL'];
                }

                public function client(): string
                {
                    return $this->properties['client'];
                }

                public function tags(): array
                {
                    return $this->properties['tags'];
                }

                public function maintainer(): string
                {
                    return $this->properties['maintainer'];
                }

                public function maintainerURL(): string
                {
                    return $this->properties['maintainerURL'];
                }

                public function targetNamePlatform(): string
                {
                    return $this->properties['targetNamePlatform'];
                }

                public function targetVersionPlatform(): string
                {
                    return $this->properties['targetVersionPlatform'];
                }

                public function downloads(): array
                {
                    return $this->properties['downloads'];
                }
            };
        };
    }

    $faker = Factory::create();

    return $source(
        [
            'name'                  => $override['name'] ?? $faker->colorName,
            'type'                  => $override['type'] ?? 'module',
            'element'               => $override['element'] ?? "mod_{$faker->colorName}",
            'version'               => $override['version'] ?? $faker->numerify('#.#.#'),
            'description'           => $override['description'] ?? $faker->sentence(6, true),
            'infoURL'               => $override['infoURL'] ?? $faker->url,
            'client'                => $override['client'] ?? 'site',
            'tags'                  => $override['tags'] ?? ['stable'],
            'maintainer'            => $override['maintainer'] ?? $faker->name,
            'maintainerURL'         => $override['maintainerURL'] ?? $faker->url,
            'targetNamePlatform'    => $override['targetNamePlatform'] ?? 'joomla',
            'targetVersionPlatform' => $override['targetVersionPlatform'] ?? '3.0',
            'downloads'             => $override['downloads'] ?? [
                    [
                        'format' => 'zip',
                        'type'   => 'upgrade',
                        'url'    => $faker->url,
                    ],
                ],
        ]
    );
}


/**
 * Trait UpdateMaker
 *
 * @package JEXUpdate\Tests\Unit
 */
trait UpdateMaker
{
    /**
     * Build an Update aggregate.
     *
     * @param  array  $override
     * @param  callable|null  $source
     *
     * @return Update
     * @throws UpdateAssemblingFailure
     */
    public function buildUpdate(array $override = [], ?callable $source = null): Update
    {
        return (new UpdateAssembler())->assemble(makeUpdateSource($override, $source));
    }
}
