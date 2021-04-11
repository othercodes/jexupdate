<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Application;

use Exception;
use JEXUpdate\Extensions\Application\Contracts\ExtensionSource;
use JEXUpdate\Extensions\Domain\Exceptions\ExtensionAssemblingFailure;
use JEXUpdate\Extensions\Domain\Extension;
use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Domain\ValueObjects\Name;
use JEXUpdate\Shared\Domain\ValueObjects\Type;
use JEXUpdate\Shared\Domain\ValueObjects\Version;

/**
 * Class ExtensionAssembler
 *
 * @package JEXUpdate\Extensions\Application
 */
final class ExtensionAssembler
{
    /**
     * @param  ExtensionSource  $source
     *
     * @return Extension
     * @throws ExtensionAssemblingFailure
     */
    public function assemble(ExtensionSource $source): Extension
    {
        try {
            return new Extension(
                new Name($source->name()),
                new Version($source->version()),
                new Element($source->element()),
                new Type($source->type())
            );
        } catch (Exception $e) {
            throw new ExtensionAssemblingFailure(
                "Unable to build Extension {$source->element()} due: {$e->getMessage()}."
            );
        }
    }
}
