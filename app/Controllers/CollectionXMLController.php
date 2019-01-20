<?php

namespace JEXUpdate\Controllers;

use JEXUpdate\Core\Controller;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CollectionXMLController
 * @package JEXUpdate\Controllers
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
        if ($this->isCacheInvalid(ROOT_PATH . '/cache/index.xml')) {

            $this->logger->info("Cache file is not valid, generating new file!");

            $extensionSet = $this->dom->createElement('extensionset');
            $extensionSet->setAttribute('name', $this->jexupdate['server']['name']);
            $extensionSet->setAttribute('description', $this->jexupdate['server']['description']);

            foreach ($this->jexupdate['repositories'] as $extensionName => $vendor) {

                $this->logger->info("Processing $vendor/$extensionName");

                try {

                    $latest = $this->client
                        ->request('GET', "/repos/$vendor/$extensionName/releases/latest")
                        ->getBody();

                    $this->logger->debug("Raw payload: $latest");

                    $latest = json_decode($latest);
                    if (!isset($latest->assets[0]->browser_download_url)) {
                        $this->logger->warning("$vendor/$extensionName don't have a valid zip installer asset.");
                        continue;
                    }

                    $type = $this->getExtType($extensionName);

                    try {

                        $manifest = $this->client
                            ->request('GET', "/repos/$vendor/$extensionName/contents/$extensionName.xml");

                    } catch (\GuzzleHttp\Exception\GuzzleException $e) {

                        $this->logger->warning("Unable to find extension manifest $extensionName.xml, processing as template...");
                        $manifest = $this->client
                            ->request('GET', "/repos/$vendor/$extensionName/contents/templateDetails.xml");

                        $type = 'template';
                    }

                    $manifest = $manifest->getBody();

                    $this->logger->debug("Raw manifest: $manifest");

                    $manifest = \DOMDocument::loadXML(base64_decode(json_decode($manifest)->content));
                    $client = $manifest->getElementsByTagName('extension')->item(0)->getAttributeNode('client')->value;

                    $extension = $this->dom->createElement('extension');
                    $extension->setAttribute('name', $extensionName);
                    $extension->setAttribute('element', $extensionName);
                    $extension->setAttribute('type', $type);
                    $extension->setAttribute('client', $client);
                    $extension->setAttribute('client_id', $client);
                    $extension->setAttribute('version', ltrim($latest->tag_name, 'v'));
                    $extension->setAttribute('detailsurl', $request->getUri()->withPath("$extensionName.xml"));

                    $extensionSet->appendChild($extension);

                } catch (\Exception $e) {

                    $this->logger->error($e->getMessage());
                }
            }

            $this->dom->appendChild($extensionSet);

            $xml = $this->dom->saveXML();
            file_put_contents(ROOT_PATH . '/cache/index.xml', $xml);

        } else {
            $this->logger->info("Loading xml from cache.");
            $xml = file_get_contents(ROOT_PATH . '/cache/index.xml');
        }

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');

        $response->getBody()->write($xml);

        return $response;

    }
}