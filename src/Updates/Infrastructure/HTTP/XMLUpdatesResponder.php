<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Infrastructure\HTTP;

use DOMDocument;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class XMLUpdatesResponder
 *
 * @package JEXUpdate\Updates\Infrastructure\HTTP
 */
final class XMLUpdatesResponder
{
    /**
     * Build the response to be returned.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $data
     *
     * @return Response
     */
    public function index(Request $request, Response $response, array $data = []): Response
    {
        if (empty($data['updates'])) {
            $response = $response->withStatus(404);
            $response = $response->withHeader('Content-Type', 'text/html');
            $response->getBody()->write('Extension not found');

            return $response;
        }

        $dom = new DOMDocument('1.0', 'utf-8');

        $updates = $dom->createElement('updates');

        foreach ($data['updates'] as $item) {
            $update = $dom->createElement('update');
            $update->appendChild($dom->createElement('name', $item->name));
            $update->appendChild($dom->createElement('description', $item->description));
            $update->appendChild($dom->createElement('element', $item->element));
            $update->appendChild($dom->createElement('type', $item->type));
            $update->appendChild($dom->createElement('version', $item->version));
            $update->appendChild($dom->createElement('infourl', $item->infourl));
            $update->appendChild($dom->createElement('client', "{$item->client}"));

            $downloads = $dom->createElement('downloads');
            foreach ($item->downloads as $url) {
                $downloadURL = $dom->createElement('downloadurl', $url->url);
                $downloadURL->setAttribute('type', $url->type);
                $downloadURL->setAttribute('format', $url->format);
                $downloads->appendChild($downloadURL);
            }
            $update->appendChild($downloads);

            $tags = $dom->createElement('tags');
            foreach ($item->tags as $tag) {
                $tags->appendChild($dom->createElement('tag', $tag));
            }
            $update->appendChild($tags);

            $update->appendChild($dom->createElement('maintainer', $item->maintainer));
            $update->appendChild($dom->createElement('maintainerurl', $item->maintainerurl));

            $platform = $dom->createElement('targetplatform');
            $platform->setAttribute('name', $item->targetplatform->name);
            $platform->setAttribute('version', $item->targetplatform->version);
            $update->appendChild($platform);

            $updates->appendChild($update);
        }

        $dom->appendChild($updates);

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');
        $response->getBody()->write($dom->saveXML());

        return $response;
    }
}
