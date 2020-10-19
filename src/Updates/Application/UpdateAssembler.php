<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application;

use Exception;
use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Domain\ValueObjects\InfoURL;
use JEXUpdate\Shared\Domain\ValueObjects\Name;
use JEXUpdate\Shared\Domain\ValueObjects\Type;
use JEXUpdate\Shared\Domain\ValueObjects\Version;
use JEXUpdate\Updates\Application\Contracts\UpdateSource;
use JEXUpdate\Updates\Domain\Exceptions\UpdateAssemblingFailure;
use JEXUpdate\Updates\Domain\Update;
use JEXUpdate\Updates\Domain\ValueObjects\Client;
use JEXUpdate\Updates\Domain\ValueObjects\Description;
use JEXUpdate\Updates\Domain\ValueObjects\DownloadURL;
use JEXUpdate\Updates\Domain\ValueObjects\Maintainer;
use JEXUpdate\Updates\Domain\ValueObjects\Tag;
use JEXUpdate\Updates\Domain\ValueObjects\TargetPlatform;
use Psr\Http\Message\UriInterface;

/**
 * Class UpdateAssembler
 *
 * @package JEXUpdate\Updates\Application
 */
final class UpdateAssembler
{
    /**
     * @param UpdateSource $source
     *
     * @return Update
     * @throws UpdateAssemblingFailure
     */
    public function assemble(UpdateSource $source): Update
    {
        try {
            $update = new Update(
                new Name($source->name()),
                new Description($source->description()),
                new Version($source->version()),
                new Element($source->element()),
                new Type($source->type()),
                new Client($source->client()),
                new InfoURL($source->infoURL()),
                new Maintainer(
                    $source->maintainer(),
                    $source->maintainerURL()
                ),
                new TargetPlatform(
                    $source->targetNamePlatform(),
                    $source->targetVersionPlatform()
                ),
            );

            $update->addTag(
                ...array_map(fn(string $tag) => new Tag($tag), $source->tags())
            );

            $update->addDownloadURL(
                ...array_map(
                    fn(array $download) => new DownloadURL(
                        $download['url'],
                        $download['format'],
                        $download['type']
                    ),
                    $source->downloads()
                )
            );

            return $update;
        } catch (Exception $e) {
            throw new UpdateAssemblingFailure(
                "Unable to build Extension Update #{$source->version()} due: {$e->getMessage()}."
            );
        }
    }
}
