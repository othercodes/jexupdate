<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application\Contracts;

/**
 * Interface UpdateSource
 *
 * @package JEXUpdate\Updates\Application\Contracts
 */
interface UpdateSource
{
    /**
     * Provides access to the extension name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Provides access to the extension description.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Provides access to the element version.
     *
     * @return string
     */
    public function version(): string;

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
     * Provides access to the info url. A URL to point users to containing
     * information about the update (optional) (In CMS 2.5, if set, this URL
     * will be displayed in the update view)
     *
     * @return string
     */
    public function infoURL(): string;

    /**
     * Provides access to the client type. Required for modules and templates
     * as of 3.2.0. – The client ID of the extension, which can be found by
     * looking inside the #_extensions table. To date, use 0 for "site" and 1
     * for "administrator".
     *
     * @return string
     */
    public function client(): string;

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
    public function tags(): array;

    /**
     * Provides access to the maintainer full name.
     *
     * @return string
     */
    public function maintainer(): string;

    /**
     * Provides access to the maintainer web site.
     *
     * @return string
     */
    public function maintainerURL(): string;

    /**
     * Provides access to the platform name, is always 'joomla'.
     *
     * @return string
     */
    public function targetNamePlatform(): string;

    /**
     * Provides access to he target version platform, i.e: 3.[23456789]
     *
     * @return string
     */
    public function targetVersionPlatform(): string;

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
    public function downloads(): array;
}
