<?php

namespace JEXUpdate\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CollectionXMLController
 * @package JEXUpdate\Controllers
 */
class ExtensionXMLController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function extension(Request $request, Response $response)
    {
        $extensionName = current(explode('.', $request->getAttribute('extension'), 2));
        if (!array_key_exists($extensionName, $this->jexupdate['repositories'])) {
            return $response->withStatus(404);
        }

        if ($this->isCacheInvalid(ROOT_PATH . "/cache/$extensionName.xml")) {
            $this->logger->info("Cache file is not valid, generating new file!");

            $vendor = $this->jexupdate['repositories'][$extensionName];

            $latest = $this->client
                ->request('GET', "/repos/$vendor/$extensionName/releases/latest")
                ->getBody();

            $this->logger->debug("Raw payload: $latest");

            $latest = json_decode($latest);
            if (!isset($latest->assets[0]->browser_download_url)) {
                $this->logger->warning("$vendor/$extensionName don't have a valid zip installer asset.");
                return $response->withStatus(404);
            }

            $format = explode('.', $latest->assets[0]->name);
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


            /*********************************************************************
             *                           XML Creation
             *********************************************************************/

            $client = $manifest->getElementsByTagName('extension')->item(0)->getAttributeNode('client')->value;

            $updates = $this->dom->createElement('updates');

            $update = $this->dom->createElement('update');
            $update->appendChild($this->dom->createElement('name',
                $manifest->getElementsByTagName('name')->item(0)->nodeValue)
            );
            $update->appendChild($this->dom->createElement('description',
                $manifest->getElementsByTagName('name')->item(0)->nodeValue)
            );
            $update->appendChild($this->dom->createElement('element', $extensionName));
            $update->appendChild($this->dom->createElement('type', $this->getExtType($extensionName)));
            $update->appendChild($this->dom->createElement('version', ltrim($latest->tag_name, 'v')));
            $update->appendChild($this->dom->createElement('infourl', $latest->html_url));
            $update->appendChild($this->dom->createElement('client', $client));
            $downloads = $this->dom->createElement('downloads');

            $downloadurl = $this->dom->createElement('downloadurl', $latest->assets[0]->browser_download_url);
            $downloadurl->setAttribute('type', 'upgrade');
            $downloadurl->setAttribute('format', end($format));

            $downloads->appendChild($downloadurl);
            $update->appendChild($downloads);

            $tags = $this->dom->createElement('tags');
            $tags->appendChild($this->dom->createElement('tag', 'stable'));
            $update->appendChild($tags);

            $update->appendChild($this->dom->createElement('maintainer',
                $manifest->getElementsByTagName('author')->item(0)->nodeValue
            ));
            $update->appendChild($this->dom->createElement('maintainerurl',
                $manifest->getElementsByTagName('authorUrl')->item(0)->nodeValue
            ));

            $jversion = $this->dom->createElement('targetplatform');
            $jversion->setAttribute('name', 'joomla');
            $jversion->setAttribute('version', '3.[23456789]');

            $update->appendChild($jversion);
            $updates->appendChild($update);
            $this->dom->appendChild($updates);

            $xml = $this->dom->saveXML();
            file_put_contents(ROOT_PATH . "/cache/$extensionName.xml", $xml);

        } else {
            $this->logger->info("Loading xml from cache.");
            $xml = file_get_contents(ROOT_PATH . "/cache/$extensionName.xml");
        }

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');

        $response->getBody()->write($xml);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function collection(Request $request, Response $response)
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