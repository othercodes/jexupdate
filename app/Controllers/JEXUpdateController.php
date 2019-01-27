<?php

namespace JEXUpdate\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CollectionXMLController
 * @package JEXUpdate\Controllers
 */
class JEXUpdateController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response)
    {
        $extension = $request->getAttribute('extension');

        if (isset($extension)) {
            $extension = current(explode('.', $extension, 2));
            if (!array_key_exists($extension, $this->jexupdate['repositories'])) {
                return $response->withStatus(404);
            }

        } else {
            $extension = 'index';
        }

        if ($this->isCacheInvalid(ROOT_PATH . "/cache/$extension.xml")) {

            $this->logger->info("Cache file (/cache/$extension.xml) is not valid, generating new file!");

            $dom = new \DOMDocument('1.0', 'utf-8');
            if ($extension === 'index') {

                $extensionSet = $dom->createElement('extensionset');
                $extensionSet->setAttribute('name', $this->jexupdate['server']['name']);
                $extensionSet->setAttribute('description', $this->jexupdate['server']['description']);

                foreach ($this->jexupdate['repositories'] as $extensionName => $vendor) {

                    $manifest = $this->getManifest($vendor, $extension);
                    if (!isset($manifest)) {
                        continue;
                    }

                    $type = $this->getExtType($extension);
                    $latest = $this->getLatestRelease($vendor, $extension);

                    $client = $manifest->getElementsByTagName('extension')->item(0)->getAttributeNode('client')->value;

                    $extension = $dom->createElement('extension');
                    $extension->setAttribute('name', $extensionName);
                    $extension->setAttribute('element', $extensionName);
                    $extension->setAttribute('type', $type);
                    $extension->setAttribute('client', $client);
                    $extension->setAttribute('client_id', $client);
                    $extension->setAttribute('version', ltrim($latest->tag_name, 'v'));
                    $extension->setAttribute('detailsurl', $request->getUri()->withPath("$extensionName.xml"));

                    $extensionSet->appendChild($extension);
                }

                $dom->appendChild($extensionSet);
            }

            $xml = $dom->saveXML();
            file_put_contents(ROOT_PATH . "/cache/$extension.xml", $xml);

        } else {

            $this->logger->info("Loading file (/cache/$extension.xml) from cache.");
            $xml = file_get_contents(ROOT_PATH . "/cache/$extension.xml");
        }

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');

        $response->getBody()->write($xml);

        return $response;

    }

    /**
     * @param string $vendor
     * @param string $extension
     * @return mixed|\Psr\Http\Message\StreamInterface|null
     */
    protected function getLatestRelease($vendor, $extension)
    {
        try {

            $latest = $this->client
                ->request('GET', "/repos/$vendor/$extension/releases/latest")
                ->getBody();

            $this->logger->debug("Raw payload: $latest");

            $latest = json_decode($latest);
            if (!isset($latest->assets[0]->browser_download_url)) {
                $this->logger->warning("$vendor/$extension don't have a valid zip installer asset.");
                return null;
            }

            return $latest;

        } catch (\GuzzleHttp\Exception\GuzzleException $e) {

            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param string $vendor
     * @param string $extension
     * @return mixed|null
     */
    protected function getManifest($vendor, $extension)
    {
        $this->logger->info("Processing $vendor/$extension");

        try {

            $manifest = $this->client
                ->request('GET', "/repos/$vendor/$extension/contents/$extension.xml");

        } catch (\GuzzleHttp\Exception\GuzzleException $e) {

            try {

                $this->logger->warning("Unable to find extension manifest $extension.xml, processing as template...");
                $manifest = $this->client
                    ->request('GET', "/repos/$vendor/$extension/contents/templateDetails.xml");

            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                $this->logger->error($e->getMessage());
                return null;
            }
        }

        $manifest = $manifest->getBody();

        $this->logger->debug("Raw payload: $manifest");
        return \DOMDocument::loadXML(base64_decode(json_decode($manifest)->content));
    }
}