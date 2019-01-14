<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CollectionXMLController
 * @package App
 */
class CollectionXMLController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request, Response $response)
    {
        $extensionSet = $this->dom->createElement('extensionset');
        $extensionSet->setAttribute('name', 'otherCode Extensions');
        $extensionSet->setAttribute('description', 'otherCode Extension Set');

        foreach ($this->service['repositories'] as $repository => $vendor) {

            try {

                $payload = json_decode($this->client->request('GET',
                    '/repos/' . $vendor . '/' . $repository . '/releases/latest')->getBody());

                $this->logger->debug(print_r($payload, true));

                $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();

                $extension = $this->dom->createElement('extension');
                $extension->setAttribute('name', $repository);
                $extension->setAttribute('element', $repository);
                $extension->setAttribute('type', $this->service['extension']['types'][substr($repository, 0, 3)]);
                $extension->setAttribute('version', ltrim($payload->tag_name, 'v'));
                $extension->setAttribute('detailsurl', $baseUrl . '/' . $repository . '.xml');

                $extensionSet->appendChild($extension);

            } catch (\Exception $e) {

                $this->logger->error($e->getMessage());
            }
        }

        $this->dom->appendChild($extensionSet);

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');

        $body = $response->getBody();
        $body->write($this->dom->saveXML());

        return $response;

    }
}