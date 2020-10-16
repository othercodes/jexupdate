<?php

namespace JEXServer\Service\Github;

use Psr\Log\LoggerInterface;

/**
 * Class Client
 * @package JEXServer\Service\Github
 */
class Client
{
    /**
     * Main configuration
     * @var array
     */
    protected $configuration;

    /**
     * HTTP Client
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Logger
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Client constructor.
     * @param array $configuration
     * @param \GuzzleHttp\Client $http
     * @param LoggerInterface $logger
     */
    public function __construct(array $configuration, \GuzzleHttp\Client $http, LoggerInterface $logger)
    {
        $this->configuration = $configuration;
        $this->http = $http;
        $this->logger = $logger;
    }

    /**
     * @param $vendor
     * @param $repository
     * @return mixed|null
     */
    public function getLatestRelease($vendor, $repository)
    {
        try {
            $this->logger->info("Retrieving latest release of $vendor/$repository.");

            $latest = $this->http
                ->request('GET', "/repos/$vendor/$repository/releases/latest", [
                    'base_uri' => $this->configuration['uri'],
                    'headers' => [
                        'Authorization' => 'token ' . $this->configuration['token']
                    ]
                ])
                ->getBody();

            $this->logger->debug("Raw payload: $latest");

            return json_decode($latest);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param $vendor
     * @param $repository
     * @param $file
     * @return mixed|null
     */
    public function getFile($vendor, $repository, $file)
    {
        $this->logger->info("Retrieving file $file from $vendor/$repository.");

        try {
            $file = $this->http
                ->request('GET', "/repos/$vendor/$repository/contents/$file", [
                    'base_uri' => $this->configuration['uri'],
                    'headers' => [
                        'Authorization' => 'token ' . $this->configuration['token']
                    ]
                ])
                ->getBody();

            $this->logger->debug("Raw payload: $file");

            return json_decode($file);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}
