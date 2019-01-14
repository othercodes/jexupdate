<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Controller
 * @property \DOMDocument $dom
 * @property Client $client
 * @property LoggerInterface $logger
 * @property array $service
 * @package App\Controllers
 */
abstract class Controller
{
    /**
     * Dependency Container
     * @var ContainerInterface
     */
    protected $_container;

    /**
     * Controller constructor.
     * @param $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * Provide an access to the common libraries of the controller
     * @param string $id
     * @return object
     */
    public function __get($id)
    {
        return ($this->_container->has($id))
            ? $this->_container->get($id)
            : null;
    }
}