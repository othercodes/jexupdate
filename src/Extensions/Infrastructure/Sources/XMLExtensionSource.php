<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Infrastructure\Sources;

use DOMDocument;
use JEXUpdate\Extensions\Application\Contracts\ExtensionSource;
use JEXUpdate\Shared\Domain\ValueObjects\Element;

/**
 * Class XMLExtensionSource
 *
 * @package JEXUpdate\Extensions\Infrastructure\Sources
 */
final class XMLExtensionSource implements ExtensionSource
{
    /**
     * The element extension name.
     *
     * @var Element
     */
    private Element $id;

    /**
     * The extension xml manifest.
     *
     * @var DOMDocument
     */
    private DOMDocument $manifest;

    /**
     * The release for the given extension.
     *
     * @var object
     */
    private object $release;

    /**
     * XMLUpdateSource constructor.
     *
     * @param Element     $id
     * @param DOMDocument $manifest
     * @param object      $release
     */
    public function __construct(Element $id, DOMDocument $manifest, object $release)
    {
        $this->id = $id;
        $this->manifest = $manifest;
        $this->release = $release;
    }

    /**
     * Provides access to the extension name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->manifest
            ->getElementsByTagName('name')
            ->item(0)
            ->nodeValue;
    }

    /**
     * Provides access to the element version.
     *
     * @return string
     */
    public function version(): string
    {
        return ltrim($this->release->tag_name, 'v');
    }

    /**
     * Provides access to the element information.
     *
     * @return string
     */
    public function element(): string
    {
        return $this->id->value();
    }

    /**
     * Provides access to the extension type.
     *
     * @return string
     */
    public function type(): string
    {
        return $this->manifest
            ->getElementsByTagName('extension')
            ->item(0)
            ->attributes
            ->getNamedItem('type')
            ->value;
    }
}
