<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Application\Contracts;

/**
 * Interface ExtensionSource
 *
 * @package JEXUpdate\Extensions\Application\Contracts
 */
interface ExtensionSource
{
    /**
     * Provides access to the extension name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Provides access to the element information.
     *
     * @return string
     */
    public function element(): string;

    /**
     * Provides access to the extension type.
     *
     * @return string
     */
    public function type(): string;

    /**
     * Provides access to the element version.
     *
     * @return string
     */
    public function version(): string;

    /**
     * Provides access to the URL of the XML file which contains that extension's
     * individual update definitions.
     *
     * @return string
     */
    public function detailURL(): string;
}
