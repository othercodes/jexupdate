<?php

namespace JEXUpdate\Controllers;

use JEXUpdate\Core\Controller;
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
    public function index(Request $request, Response $response)
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

            $manifest = $this->client
                ->request('GET', "/repos/$vendor/$extensionName/contents/$extensionName.xml")
                ->getBody();

            $this->logger->debug("Raw manifest: $manifest");

            $manifest = \DOMDocument::loadXML(base64_decode(json_decode($manifest)->content));
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
            $update->appendChild($this->dom->createElement('version',
                $manifest->getElementsByTagName('version')->item(0)->nodeValue
            ));
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
}