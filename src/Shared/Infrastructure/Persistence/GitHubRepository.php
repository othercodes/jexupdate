<?php

declare(strict_types=1);

namespace JEXUpdate\Shared\Infrastructure\Persistence;

use DOMDocument;
use Exception;
use GuzzleHttp\Client as HTTP;
use GuzzleHttp\Exception\GuzzleException;
use JEXUpdate\Shared\Domain\ValueObjects\Element;

/**
 * Class GitHubRepository
 *
 * @package JEXUpdate\Shared\Infrastructure\Persistence
 */
abstract class GitHubRepository
{
    /**
     * HTTP Client
     *
     * @var HTTP
     */
    protected HTTP $http;

    /**
     * Main configuration
     *
     * @var GitHubConfiguration
     */
    protected GitHubConfiguration $configuration;

    /**
     * Client constructor.
     *
     * @param GitHubConfiguration $configuration
     * @param HTTP                $http
     */
    public function __construct(GitHubConfiguration $configuration, HTTP $http)
    {
        $this->configuration = $configuration;
        $this->http = $http;
    }

    /**
     * Retrieve extension manifest xml file from the repository.
     *
     * @param Element $id
     * @param string  $ref
     *
     * @return DOMDocument
     * @throws Exception
     */
    protected function getExtensionManifest(Element $id, string $ref = 'master'): DOMDocument
    {
        switch ($id->prefix()) {
            case 'tpl':
                $file = "templateDetails.xml";
                break;
            case 'com':
                $file = "administrator/components/{$id}/{$id->name()}.xml";
                break;
            default:
                $file = "{$id->value()}.xml";
        }

        try {
            $file = $this->http
                ->request(
                    'GET',
                    "/repos/{$this->configuration->account}/{$id}/contents/$file?" . http_build_query(
                        ['ref' => $ref]
                    ),
                    [
                        'base_uri' => $this->configuration->uri,
                        'headers'  => [
                            'Accept'        => 'application/vnd.github.v3+json',
                            'Authorization' => "token {$this->configuration->token}",
                        ],
                    ]
                )
                ->getBody()
                ->getContents();

            $manifest = new DOMDocument();
            $manifest->loadXML(base64_decode(json_decode($file)->content));

            return $manifest;
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
