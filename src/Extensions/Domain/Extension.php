<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Domain;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Domain\ValueObjects\Name;
use JEXUpdate\Shared\Domain\ValueObjects\Type;
use JEXUpdate\Shared\Domain\ValueObjects\Version;
use OtherCode\DDDValueObject\HasInvariants;

/**
 * Class Extension
 *
 * @package JEXUpdate\Extensions\Domain
 */
final class Extension
{
    use HasInvariants;

    private Name $name;

    private Version $version;

    private Element $element;

    private Type $type;

    /**
     * Extension constructor.
     *
     * @param  Name  $name
     * @param  Version  $version
     * @param  Element  $element
     * @param  Type  $type
     */
    public function __construct(Name $name, Version $version, Element $element, Type $type)
    {
        $this->name = $name;
        $this->version = $version;
        $this->element = $element;
        $this->type = $type;

        $this->checkInvariants();
    }

    public function invariantExtensionTypeMustMatchElementPrefix(): bool
    {
        return $this->element->prefix() === $this->type->prefix();
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function version(): Version
    {
        return $this->version;
    }

    public function element(): Element
    {
        return $this->element;
    }

    public function type(): Type
    {
        return $this->type;
    }
}
