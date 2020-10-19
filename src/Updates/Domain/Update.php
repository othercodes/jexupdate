<?php

namespace JEXUpdate\Updates\Domain;

use Illuminate\Support\Collection;
use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Domain\ValueObjects\InfoURL;
use JEXUpdate\Shared\Domain\ValueObjects\Name;
use JEXUpdate\Shared\Domain\ValueObjects\Type;
use JEXUpdate\Shared\Domain\ValueObjects\Version;
use JEXUpdate\Updates\Domain\ValueObjects\Client;
use JEXUpdate\Updates\Domain\ValueObjects\Description;
use JEXUpdate\Updates\Domain\ValueObjects\DownloadURL;
use JEXUpdate\Updates\Domain\ValueObjects\Maintainer;
use JEXUpdate\Updates\Domain\ValueObjects\Tag;
use JEXUpdate\Updates\Domain\ValueObjects\TargetPlatform;

/**
 * Class Extension
 *
 * @package JEXUpdate\Updates\Domain
 */
final class Update
{
    private Name $name;

    private Description $description;

    private Version $version;

    private Element $element;

    private Type $type;

    private Client $client;

    private InfoURL $infoURL;

    private Maintainer $maintainer;

    private TargetPlatform $target;

    /**
     * List of download URL's.
     *
     * @var Collection|DownloadURL[]
     */
    private Collection $downloads;

    /**
     * List of tags for the release.
     *
     * @var Collection|Tag[]
     */
    private Collection $tags;

    /**
     * Update constructor.
     *
     * @param Name           $name
     * @param Description    $description
     * @param Element        $element
     * @param Type           $type
     * @param Version        $version
     * @param Client         $client
     * @param InfoURL        $infoURL
     * @param Maintainer     $maintainer
     * @param TargetPlatform $target
     */
    public function __construct(
        Name $name,
        Description $description,
        Version $version,
        Element $element,
        Type $type,
        Client $client,
        InfoURL $infoURL,
        Maintainer $maintainer,
        TargetPlatform $target
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
        $this->element = $element;
        $this->type = $type;
        $this->client = $client;
        $this->infoURL = $infoURL;
        $this->maintainer = $maintainer;
        $this->target = $target;

        $this->tags = new Collection();
        $this->downloads = new Collection();
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function description(): Description
    {
        return $this->description;
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

    public function client(): Client
    {
        return $this->client;
    }

    public function infoURL(): InfoURL
    {
        return $this->infoURL;
    }

    public function maintainer(): Maintainer
    {
        return $this->maintainer;
    }

    public function target(): TargetPlatform
    {
        return $this->target;
    }

    /**
     * Return the tags collection.
     *
     * @return Collection|Tag[]
     */
    public function tags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag ...$tags): void
    {
        $this->tags = $this->tags->merge($tags);
    }

    /**
     * Return the downloads collection.
     *
     * @return Collection|DownloadURL[]
     */
    public function downloads(): Collection
    {
        return $this->downloads;
    }

    public function addDownloadURL(DownloadURL ...$downloadURLs): void
    {
        $this->downloads = $this->downloads->merge($downloadURLs);
    }
}
