<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application;

use JEXUpdate\Updates\Domain\Update;
use JEXUpdate\Updates\Domain\ValueObjects\DownloadURL;
use JEXUpdate\Updates\Domain\ValueObjects\Tag;

/**
 * Class UpdateDTOAssembler
 *
 * @package JEXUpdate\Updates\Application
 */
final class UpdateDTOAssembler
{
    /**
     * Assemble a DTO with the given Update aggregate.
     *
     * @param  Update  $update
     *
     * @return object
     */
    public function assemble(Update $update): object
    {
        return (object)[
            'name'           => $update->name()->value(),
            'description'    => $update->description()->value(),
            'version'        => $update->version()->value(),
            'element'        => $update->element()->value(),
            'type'           => $update->type()->value(),
            'client'         => $update->client()->value(),
            'infourl'        => $update->infoURL()->value(),
            'maintainer'     => $update->maintainer()->name(),
            'maintainerurl'  => $update->maintainer()->url(),
            'targetplatform' => (object)[
                'name'    => $update->target()->name(),
                'version' => $update->target()->version(),
            ],
            'tags'           => array_map(
                fn(Tag $tag) => $tag->value(),
                $update->tags()->all()
            ),
            'downloads'      => array_map(
                fn(DownloadURL $download) => (object)[
                    'type'   => $download->type(),
                    'format' => $download->format(),
                    'url'    => $download->url(),
                ],
                $update->downloads()->all()
            ),
        ];
    }
}
