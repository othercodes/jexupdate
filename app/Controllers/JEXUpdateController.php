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
    public function test(Request $request, Response $response)
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



        }


        $response = $response->withStatus(200);
        //$response = $response->withHeader('Content-Type', 'application/xml');

        $response->getBody()->write('hello world');

        return $response;

    }
}