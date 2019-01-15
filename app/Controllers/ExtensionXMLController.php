<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CollectionXMLController
 * @package App
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
        $extension = current(explode('.', $request->getAttribute('extension'), 2));
        if (!array_key_exists($extension, $this->service['repositories'])) {
            return $response->withStatus(404);
        }

        $payload = json_decode($this->client->request('GET',
            '/repos/' . $this->service['repositories'][$extension] . '/' . $extension . '/releases/latest')->getBody());

        $this->logger->debug(print_r($payload, true));

        if (!isset($payload->assets[0]->browser_download_url)) {
            return $response->withStatus(404);
        }

        $updates = $this->dom->createElement('updates');

        $update = $this->dom->createElement('update');
        $update->appendChild($this->dom->createElement('name', $extension));
        $update->appendChild($this->dom->createElement('description', $extension));
        $update->appendChild($this->dom->createElement('element', $extension));
        $update->appendChild($this->dom->createElement('type',
            $this->service['extension']['types'][substr($extension, 0, 3)]));
        $update->appendChild($this->dom->createElement('version', ltrim($payload->tag_name, 'v')));
        $update->appendChild($this->dom->createElement('infourl', $payload->html_url));
        $update->appendChild($this->dom->createElement('client', 1));

        $downloads = $this->dom->createElement('downloads');
        $downloads->appendChild($this->dom->createElement('downloadurl', $payload->assets[0]->browser_download_url));
        $update->appendChild($downloads);

        $jversion = $this->dom->createElement('targetplatform');
        $jversion->setAttribute('name', 'joomla');
        $jversion->setAttribute('version', '3.[234567]');

        $update->appendChild($jversion);
        $updates->appendChild($update);
        $this->dom->appendChild($updates);

        $response = $response->withStatus(200);
        $response = $response->withHeader('Content-Type', 'application/xml');

        $body = $response->getBody();
        $body->write($this->dom->saveXML());

        return $response;
    }
}