<?php

namespace JEXUpdate\Shared\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\StringValueObject;
use OtherCode\DDDValueObject\IsValueObject;

/**
 * Class Element
 *
 * @property  string value
 *
 * @package JEXUpdate\Shared\Domain\ValueObjects
 */
final class Element extends StringValueObject
{
    use IsValueObject;

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
    protected int $maxLength = 255;

    /**
     * Return the extension prefix.
     *
     * @return string
     */
    public function prefix(): string
    {
        return substr($this->value, 0, 3);
    }
}
