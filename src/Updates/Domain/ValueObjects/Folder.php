<?php

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\StringValueObject;

/**
 * Class Folder
 *
 * @property string value
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class Folder extends StringValueObject
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
