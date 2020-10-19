<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Infrastructure\Serializers;

use DOMDocument;
use JEXUpdate\Updates\Application\Contracts\UpdateSerializer;
use JEXUpdate\Updates\Domain\Update;

/**
 * Class XMLSerializer
 *
 * @package JEXUpdate\Updates\Infrastructure\Serializers
 */
final class XMLSerializer implements UpdateSerializer
{
    /**
     * Serialize the given Update Aggregate.
     *
     * @param Update ...$aggregates
     *
     * @return DOMDocument
     */
    public function serialize(Update ...$aggregates): string
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $updates = $dom->createElement('updates');

        foreach ($aggregates as $aggregate) {
            $update = $dom->createElement('update');
            $update->appendChild(
                $dom->createElement('name', $aggregate->name()->value())
            );
            $update->appendChild(
                $dom->createElement('description', $aggregate->description()->value())
            );
            $update->appendChild(
                $dom->createElement('element', $aggregate->element()->value())
            );
            $update->appendChild(
                $dom->createElement('type', $aggregate->type()->value())
            );
            $update->appendChild(
                $dom->createElement('version', $aggregate->version()->value())
            );
            $update->appendChild(
                $dom->createElement('infourl', $aggregate->infoURL()->value())
            );
            $update->appendChild(
                $dom->createElement('client', "{$aggregate->client()->value()}")
            );

            $downloads = $dom->createElement('downloads');
            foreach ($aggregate->downloads() as $url) {
                $downloadURL = $dom->createElement('downloadurl', $url->url());
                $downloadURL->setAttribute('type', $url->type());
                $downloadURL->setAttribute('format', $url->format());
                $downloads->appendChild($downloadURL);
            }
            $update->appendChild($downloads);

            $tags = $dom->createElement('tags');
            foreach ($aggregate->tags() as $tag) {
                $tags->appendChild($dom->createElement('tag', $tag->value()));
            }
            $update->appendChild($tags);

            $update->appendChild(
                $dom->createElement('maintainer', $aggregate->maintainer()->name())
            );
            $update->appendChild(
                $dom->createElement('maintainerurl', $aggregate->maintainer()->url())
            );

            $platform = $dom->createElement('targetplatform');
            $platform->setAttribute('name', $aggregate->target()->name());
            $platform->setAttribute('version', $aggregate->target()->version());
            $update->appendChild($platform);

            $updates->appendChild($update);
        }

        $dom->appendChild($updates);

        return $dom->saveXML();
    }
}
