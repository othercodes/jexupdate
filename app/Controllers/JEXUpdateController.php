<?php

namespace JEXUpdate\Controllers;

use Psr\Http\Message\UriInterface;
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

        if (isset($extension) && $extension !== 'index.xml') {
            $extension = current(explode('.', $extension, 2));
            if (!array_key_exists($extension, $this->jexupdate['repositories'])) {
                return $response->withStatus(404);
            }

        } else {
            $extension = 'index';
        }

        if ($this->isCacheInvalid(ROOT_PATH . "/cache/$extension.xml")) {

            $this->logger->info("Cache file (/cache/$extension.xml) is not valid, generating new file!");

            switch ($extension) {
                case 'index':
                    $xml = $this->buildCollectionXML(
                        $this->jexupdate['server']['name'],
                        $this->jexupdate['server']['description'],
                        $this->jexupdate['repositories'],
                        $request->getUri()
                    );

                    break;
                default:
                    $xml = $this->buildExtensionXML(
                        $this->jexupdate['repositories'][$extension],
                        $extension
                    );
            }

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
     * @param $name
     * @param $description
     * @param array $extensions
     * @param UriInterface $uri
     * @return string
     */
    protected function buildCollectionXML($name, $description, array $extensions, UriInterface $uri)
    {
        $dom = new \DOMDocument('1.0', 'utf-8');

        $extensionSet = $dom->createElement('extensionset');

        $extensionSet->setAttribute('name', $name);
        $extensionSet->setAttribute('description', $description);

        foreach ($extensions as $extensionName => $vendor) {

            try {

                $type = $this->getExtType($extensionName);

                switch ($type) {
                    case 'template':
                        $manifest = 'templateDetails.xml';
                        break;
                    case 'component':
                        $manifest = 'administrator/components/' . $extensionName . '/' . substr($extensionName,
                                4) . '.xml';
                        break;
                    default:
                        $manifest = $extensionName . '.xml';
                }

                $this->logger->debug("Processing extension $extensionName type $type, manifest file: $manifest.");

                $file = $this->client->getFile($vendor, $extensionName, $manifest);
                if (!isset($file)) {
                    continue;
                }

                $manifest = new \DOMDocument();
                $manifest->loadXML(base64_decode($file->content));

                $client = $manifest->getElementsByTagName('extension')
                    ->item(0)->attributes->getNamedItem('client')->value;

                if (!isset($client)) {
                    $client = ($type === 'component') ? 'administrator' : 'client';
                }

                $latest = $this->client->getLatestRelease($vendor, $extensionName);
                if (!isset($latest->assets[0]->browser_download_url)) {
                    $this->logger->warning("$vendor/$extensionName don't have a valid zip installer asset.");
                    continue;
                }

                $extension = $dom->createElement('extension');
                $extension->setAttribute('name', $extensionName);
                $extension->setAttribute('element', $extensionName);
                $extension->setAttribute('type', $type);
                $extension->setAttribute('client', $client);
                $extension->setAttribute('client_id', $client);
                $extension->setAttribute('version', ltrim($latest->tag_name, 'v'));
                $extension->setAttribute('detailsurl', $uri->withPath("$extensionName.xml"));

                $extensionSet->appendChild($extension);

            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }

        $dom->appendChild($extensionSet);

        return $dom->saveXML();
    }

    /**
     * @param $vendor
     * @param $extensionName
     * @return string
     */
    protected function buildExtensionXML($vendor, $extensionName)
    {
        $dom = new \DOMDocument('1.0', 'utf-8');

        try {

            $type = $this->getExtType($extensionName);

            switch ($type) {
                case 'template':
                    $manifest = 'templateDetails.xml';
                    break;
                case 'component':
                    $manifest = 'administrator/components/' . $extensionName . '/' . substr($extensionName, 4) . '.xml';
                    break;
                default:
                    $manifest = $extensionName . '.xml';
            }

            $this->logger->debug("Processing extension $extensionName type $type, manifest file: $manifest.");

            $file = $this->client->getFile($vendor, $extensionName, $manifest);

            if (!isset($file)) {
                return null;
            }

            $manifest = new \DOMDocument();
            $manifest->loadXML(base64_decode($file->content));

            $client = $manifest->getElementsByTagName('extension')
                ->item(0)->attributes->getNamedItem('client')->value;

            if (!isset($client)) {
                $client = ($type === 'component') ? 'administrator' : 'client';
            }

            $latest = $this->client->getLatestRelease($vendor, $extensionName);
            if (!isset($latest->assets[0]->browser_download_url)) {
                $this->logger->warning("$vendor/$extensionName don't have a valid zip installer asset.");
                return null;
            }

            $updates = $dom->createElement('updates');

            $update = $dom->createElement('update');
            $update->appendChild($dom->createElement('name',
                $manifest->getElementsByTagName('name')->item(0)->nodeValue)
            );
            $update->appendChild($dom->createElement('description',
                $manifest->getElementsByTagName('name')->item(0)->nodeValue)
            );
            $update->appendChild($dom->createElement('element', $extensionName));
            $update->appendChild($dom->createElement('type', $this->getExtType($extensionName)));
            $update->appendChild($dom->createElement('version', ltrim($latest->tag_name, 'v')));
            $update->appendChild($dom->createElement('infourl', $latest->html_url));
            $update->appendChild($dom->createElement('client', $client));
            $downloads = $dom->createElement('downloads');

            $downloadurl = $dom->createElement('downloadurl', $latest->assets[0]->browser_download_url);
            $downloadurl->setAttribute('type', 'upgrade');
            $downloadurl->setAttribute('format', end($format));

            $downloads->appendChild($downloadurl);
            $update->appendChild($downloads);

            $tags = $dom->createElement('tags');
            $tags->appendChild($dom->createElement('tag', 'stable'));
            $update->appendChild($tags);

            $update->appendChild($dom->createElement('maintainer',
                $manifest->getElementsByTagName('author')->item(0)->nodeValue
            ));
            $update->appendChild($dom->createElement('maintainerurl',
                $manifest->getElementsByTagName('authorUrl')->item(0)->nodeValue
            ));

            $jversion = $dom->createElement('targetplatform');
            $jversion->setAttribute('name', 'joomla');
            $jversion->setAttribute('version', '3.[23456789]');

            $update->appendChild($jversion);
            $updates->appendChild($update);
            $dom->appendChild($updates);

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $dom->saveXML();
    }
}