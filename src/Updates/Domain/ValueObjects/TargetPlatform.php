<?php

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\IsValueObject;

/**
 * Class TargetPlatform
 *
 * @property string name
 * @property string version
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class TargetPlatform
{
    use IsValueObject;

    public function __construct(string $name, string $version)
    {
        $this->hydrate(
            [
                'name'    => $name,
                'version' => $version,
            ]
        );
    }

    public function name(): string
    {
        return $this->get('name');
    }

    public function version(): string
    {
        return $this->get('version');
    }
}
