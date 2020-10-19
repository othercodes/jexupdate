<?php

namespace JEXUpdate\Shared\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\StringValueObject;

/**
 * Class Name
 *
 * @property  string value
 *
 * @package JEXUpdate\Shared\Domain\ValueObjects
 */
final class Name extends StringValueObject
{
    /**
     * Check the min length or the string.
     *
     * @var int
     */
    protected int $minLength = 5;

    /**
     * Check the max length of the string.
     *
     * @var int
     */
    protected int $maxLength = 256;
}
