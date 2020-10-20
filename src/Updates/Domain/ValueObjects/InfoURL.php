<?php

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\StringValueObject;

/**
 * Class InfoURL
 *
 * @property  string value
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class InfoURL extends StringValueObject
{
    /**
     * Check the min length or the string.
     *
     * @var int
     */
    protected int $minLength = 10;

    /**
     * Check the max length of the string.
     *
     * @var int
     */
    protected int $maxLength = 256;

    protected function invariantUrlMustHasValidFormat(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_URL);
    }
}
