<?php

declare(strict_types=1);

namespace JEXUpdate\Tests\Unit;

use Faker\Factory;
use JEXUpdate\Extensions\Application\Contracts\ExtensionSource;
use JEXUpdate\Extensions\Application\ExtensionAssembler;
use JEXUpdate\Extensions\Domain\Exceptions\ExtensionAssemblingFailure;
use JEXUpdate\Extensions\Domain\Extension;

/**
 * Create a ExtensionSource object.
 *
 * @param  array  $override
 * @param  callable|null  $source
 *
 * @return ExtensionSource
 */
function makeExtensionSource(array $override = [], ?callable $source = null): ExtensionSource
{
    if (is_null($source)) {
        $source = function (array $properties): ExtensionSource {
            return new class($properties) implements ExtensionSource {
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
            };
        };
    }

    $faker = Factory::create();

    return $source(
        [
            'name'    => $override['name'] ?? "Epic{$faker->colorName}",
            'type'    => $override['type'] ?? 'module',
            'element' => $override['element'] ?? "mod_{epic$faker->colorName}",
            'version' => $override['version'] ?? $faker->numerify('#.#.#'),
        ]
    );
}

/**
 * Trait ExtensionMaker
 *
 * @package JEXUpdate\Tests\Unit
 */
trait ExtensionMaker
{
    /**
     * Build an Extension aggregate.
     *
     * @param  array  $override
     * @param  callable|null  $source
     *
     * @return Extension
     * @throws ExtensionAssemblingFailure
     */
    public function buildExtension(array $override = [], ?callable $source = null): Extension
    {
        return (new ExtensionAssembler())->assemble(makeExtensionSource($override, $source));
    }
}
