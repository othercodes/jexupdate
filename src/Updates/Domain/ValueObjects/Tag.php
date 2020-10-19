<?php

namespace JEXUpdate\Updates\Domain\ValueObjects;

use OtherCode\DDDValueObject\Basic\EnumeratorValueObject;

/**
 * Class Tag
 *
 * @package JEXUpdate\Updates\Domain\ValueObjects
 */
final class Tag extends EnumeratorValueObject
{
    public const DEV = 'dev';
    public const ALPHA = 'alpha';
    public const BETA = 'beta';
    public const RC = 'rc';
    public const STABLE = 'stable';
}
