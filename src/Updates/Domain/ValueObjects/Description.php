<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\StringValueObject;

/**
 * Class Description
 *
 * @property  string value
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class Description extends StringValueObject
{
    /**
     * Check the min length or the string.
     *
     * @var int
     */
    protected int $minLength = 1;

    /**
     * Check the max length of the string.
     *
     * @var int
     */
    protected int $maxLength = 256;
}
