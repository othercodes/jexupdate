<?php

namespace JEXUpdate\Shared\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\StringValueObject;

/**
 * Class Version
 *
 * @property  string value
 *
 * @package JEXUpdate\Shared\Domain\ValueObjects
 */
final class Version extends StringValueObject
{
    /**
     * Check the min length or the string.
     *
     * @var int
     */
    protected int $minLength = 3;

    /**
     * Check the max length of the string.
     *
     * @var int
     */
    protected int $maxLength = 56;

    /**
     * The pattern to match for the given string.
     *
     * @var string
     */
    protected string $pattern = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+([0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/';
}
