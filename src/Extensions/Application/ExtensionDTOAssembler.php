<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Application;

use JEXUpdate\Extensions\Domain\Extension;

/**
 * Class ExtensionDTOAssembler
 *
 * @package JEXUpdate\Extensions\Application
 */
final class ExtensionDTOAssembler
{
    /**
     * Assemble a DTO with the given Extension aggregate.
     *
     * @param  Extension  $extension
     *
     * @return object
     */
    public function assemble(Extension $extension): object
    {
        return (object)[
            'name'    => $extension->name()->value(),
            'element' => $extension->element()->value(),
            'version' => $extension->version()->value(),
            'type'    => $extension->type()->value(),
        ];
    }
}
