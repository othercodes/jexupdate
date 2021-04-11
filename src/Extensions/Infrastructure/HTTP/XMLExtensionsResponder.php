<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Infrastructure\HTTP;

use DOMDocument;
use JEXServer\Configuration;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class XMLExtensionsResponder
 *
 * @package JEXUpdate\Extensions\Infrastructure\HTTP
 */
final class XMLExtensionsResponder
{
    /**
     * The global configuration.
     *
     * @var Configuration
     */
    private Configuration $configuration;

    /**
     * XMLUpdatesResponder constructor.
     *
     * @param  Configuration  $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Build the response to be returned.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @param  array  $data
     *
     * @return Response
     */
    public function index(Request $request, Response $response, array $data = []): Response
    {
        $dom = new DOMDocument('1.0', 'utf-8');

        $extensionSet = $dom->createElement('extensionset');
        $extensionSet->setAttribute('name', $this->configuration->name);
        $extensionSet->setAttribute('description', $this->configuration->description);

        foreach ($data['extensions'] ?? [] as $item) {
            $extension = $dom->createElement('extension');
            $extension->setAttribute('name', $item->name);
            $extension->setAttribute('element', $item->element);
            $extension->setAttribute('type', $item->type);
            $extension->setAttribute('version', $item->version);
            $extension->setAttribute(
                'detailsurl',
                (string)$request->getUri()->withPath("{$item->element}.xml")
            );
            $extensionSet->appendChild($extension);
        }

        $dom->appendChild($extensionSet);

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');
        $response->getBody()->write($dom->saveXML());

        return $response;
    }
}
