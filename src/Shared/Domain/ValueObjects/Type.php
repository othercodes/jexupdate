<?php

namespace JEXUpdate\Shared\Domain\ValueObjects;

use InvalidArgumentException;
use OtherCode\DDDValueObject\Basic\EnumeratorValueObject;

/**
 * Class Type
 *
 * @method static Type template()
 * @method static Type module()
 * @method static Type plugin()
 * @method static Type component()
 * @method static Type package()
 * @method static Type library()
 *
 * @package JEXUpdate\Shared\Domain\ValueObjects
 */
final class Type extends EnumeratorValueObject
{
    public const TEMPLATE = 'template';
    public const MODULE = 'module';
    public const PLUGIN = 'plugin';
    public const COMPONENT = 'component';
    public const PACKAGE = 'package';
    public const LIBRARY = 'library';

    private static array $prefix = [
        'com' => self::COMPONENT,
        'mod' => self::MODULE,
        'plg' => self::PLUGIN,
        'tpl' => self::TEMPLATE,
        'pkg' => self::PACKAGE,
        'lib' => self::LIBRARY,
    ];

    /**
     * Naming constructor by element name.
     *
     * @param string $prefix
     *
     * @return static
     */
    public static function fromPrefix(string $prefix): self
    {
        $short = substr($prefix, 0, 3);
        if (!array_key_exists($short, self::$prefix)) {
            throw new InvalidArgumentException('Invalid extension type.');
        }

        return new self(self::$prefix[$short]);
    }

    /**
     * Return the extension type prefix.
     *
     * @return string
     */
    public function prefix(): string
    {
        return array_search($this->value, self::$prefix);
    }
}
