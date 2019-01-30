<?php

namespace JEXUpdate\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Controller
 * @property \JEXUpdate\Service\Github\Client $client
 * @property LoggerInterface $logger
 * @property array $jexupdate
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
     * Extensions types
     * @var array
     */
    protected $extensionTypes = [
        'com' => 'component',
        'mod' => 'module',
        'plg' => 'plugin',
        'tpl' => 'template',
        'pkg' => 'package',
        'lib' => 'library',
    ];

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

    /**
     * Return the extension type
     * @param string $name
     * @return null
     */
    public function getExtType($name)
    {
        $short = substr($name, 0, 3);
        if (array_key_exists($short, $this->extensionTypes)) {
            return $this->extensionTypes[$short];
        }

        return null;
    }

    /**
     * check if the cached xml is expired or not
     * @param string $path
     * @return bool
     */
    public function isCacheInvalid($path)
    {
        if (!is_readable($path)) {
            return true;
        }

        $created = Carbon::createFromTimestamp(filemtime($path));
        $created->addSeconds($this->jexupdate['cache']);

        return $created->lt(Carbon::now());

    }
}