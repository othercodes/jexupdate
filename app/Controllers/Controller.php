<?php

namespace JEXServer\Controllers;

use JEXUpdate\Extensions\Application\Actions\GetExtensionCollection;
use JEXUpdate\Extensions\Infrastructure\HTTP\XMLExtensionsResponder;
use JEXUpdate\Updates\Application\Actions\GetExtensionUpdatesCollection;
use JEXUpdate\Updates\Infrastructure\HTTP\XMLUpdatesResponder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class Controller
 *
 * @package JEXServer\Controllers
 */
class Controller
{
    /**
     * Dependency Container.
     *
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * Controller constructor.
     *
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Provide an access to the common libraries of the controller.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get(string $id)
    {
        return ($this->container->has($id))
            ? $this->container->get($id)
            : null;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $useCase = $this->__get(GetExtensionCollection::class);
        $responder = new XMLExtensionsResponder();

        return $responder->index(
            $request,
            $response,
            ['extensions' => $useCase->__invoke()]
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function extension(Request $request, Response $response): Response
    {
        $extension = current(explode('.', $request->getAttribute('extension'), 2));
        if (!array_key_exists($extension, $this->jexupdate['repositories'])) {
            return $response->withStatus(404);
        }

        $useCase = $this->__get(GetExtensionUpdatesCollection::class);
        $responder = new XMLUpdatesResponder();

        return $responder->index(
            $request,
            $response,
            ['updates' => $useCase->__invoke($extension)]
        );
    }
}
