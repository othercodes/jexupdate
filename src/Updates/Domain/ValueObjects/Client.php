<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\EnumeratorValueObject;

/**
 * Class Client
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class Client extends EnumeratorValueObject
{
    public const SITE = 0;
    public const ADMINISTRATOR = 1;
}
