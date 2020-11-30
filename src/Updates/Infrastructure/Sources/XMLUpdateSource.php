<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Infrastructure\Sources;

use DOMDocument;
use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Updates\Application\Contracts\UpdateSource;

/**
 * Class XMLUpdateSource
 *
 * @package JEXUpdate\Updates\Infrastructure\Sources
 */
final class XMLUpdateSource implements UpdateSource
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
     * @param  Element  $id
     * @param  DOMDocument  $manifest
     * @param  object  $release
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
     * Provides access to the extension description.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->manifest
            ->getElementsByTagName('description')
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

    /**
     * Provides access to the info url. A URL to point users to containing
     * information about the update (optional) (In CMS 2.5, if set, this URL
     * will be displayed in the update view)
     *
     * @return string
     */
    public function infoURL(): string
    {
        return $this->release->html_url;
    }

    /**
     * Provides access to the client type. Required for modules and templates
     * as of 3.2.0. – The client ID of the extension, which can be found by
     * looking inside the #_extensions table. To date, use 0 for "site" and 1
     * for "administrator".
     *
     * @return string
     */
    public function client(): string
    {
        return $this->manifest
            ->getElementsByTagName('extension')
            ->item(0)
            ->attributes
            ->getNamedItem('client')
            ->value;
    }

    /**
     * Provides access to the tags list:
     *  ['stable']
     *
     * A list of tags relevant to this version. Joomla! 3.4 and later uses this
     * to determine the stability level of the update. The valid tags are:
     *
     *  dev: Development versions, very unstable and pre-alpha.
     *  alpha: Alpha quality software.
     *  beta: Beta quality software.
     *  rc: Release Candidate quality software.
     *  stable: Production quality software All other tags are currently ignored.
     *
     * If you provide more than one tag containing one of the aforementioned
     * stability keywords only the LAST tag will be taken into account. If you
     * do not provide any tags Joomla! will assume it is a stable version.
     *
     * @return array
     */
    public function tags(): array
    {
        if (strpos($this->release->tag_name, '-') === false) {
            return ['stable'];
        } else {
            return array_map(
                function (string $section): string {
                    return ltrim($section, '-');
                },
                array_filter(
                    explode('.', $this->release->tag_name),
                    function (string $section): bool {
                        return strpos($section, '-') === 0;
                    }
                )
            );
        }
    }

    /**
     * Provides access to the maintainer full name.
     *
     * @return string
     */
    public function maintainer(): string
    {
        return $this->manifest
            ->getElementsByTagName('author')
            ->item(0)
            ->nodeValue;
    }

    /**
     * Provides access to the maintainer web site.
     *
     * @return string
     */
    public function maintainerURL(): string
    {
        return $this->manifest
            ->getElementsByTagName('authorUrl')
            ->item(0)
            ->nodeValue;
    }

    /**
     * Provides access to the platform name, is always 'joomla'.
     *
     * @return string
     */
    public function targetNamePlatform(): string
    {
        return 'joomla';
    }

    /**
     * Provides access to he target version platform, i.e: 3.[23456789]
     *
     * @return string
     */
    public function targetVersionPlatform(): string
    {
        return '3.[0123456789]';
    }

    /**
     * Provide access to the list of download urls:
     *  [
     *      ['type'='upgrade|full', 'format'='zip|tar', url='https://...1']
     *      ['type'='upgrade|full', 'format'='zip|tar', url='https://...2']
     *      ['type'='upgrade|full', 'format'='zip|tar', url='https://...n']
     *  ]
     *
     * @return array
     */
    public function downloads(): array
    {
        $downloads = [];
        foreach ($this->release->assets as $asset) {
            $downloads[] = [
                'type'   => $this->manifest
                        ->getElementsByTagName('extension')
                        ->item(0)
                        ->attributes
                        ->getNamedItem('method')
                        ->value ?? 'upgrade',
                'format' => pathinfo($asset->browser_download_url, PATHINFO_EXTENSION),
                'url'    => $asset->browser_download_url,
            ];
        }

        return $downloads;
    }
}
