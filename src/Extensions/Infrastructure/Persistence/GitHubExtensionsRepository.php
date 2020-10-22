<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Infrastructure\Persistence;

use Exception;
use GuzzleHttp\Client as HTTP;
use GuzzleHttp\Exception\GuzzleException;
use JEXUpdate\Extensions\Application\ExtensionAssembler;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Domain\Extension;
use JEXUpdate\Extensions\Infrastructure\Sources\XMLExtensionSource;
use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Infrastructure\Persistence\GitHubConfiguration;
use JEXUpdate\Shared\Infrastructure\Persistence\GitHubRepository;
use Psr\Http\Message\UriInterface;

/**
 * Class GitHubExtensionsRepository
 *
 * @package JEXUpdate\Extensions\Infrastructure\Persistence
 */
final class GitHubExtensionsRepository extends GitHubRepository implements ExtensionRepository
{
    /**
     * The current server uri
     *
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * The Extension assembler instance.
     *
     * @var ExtensionAssembler
     */
    private ExtensionAssembler $assembler;

    /**
     * Client constructor.
     *
     * @param GitHubConfiguration $configuration
     * @param HTTP                $http
     */
    public function __construct(GitHubConfiguration $configuration, HTTP $http)
    {
        parent::__construct($configuration, $http);
        $this->assembler = new ExtensionAssembler();
    }

    /**
     * Retrieve all the watched extensions.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws Exception
     */
    public function all(int $limit = 100, int $offset = 0): array
    {
        $all = [];
        $extensions = array_slice($this->configuration->extensions, $offset, $limit);
        foreach ($extensions as $extension) {
            if ($aggregate = $this->find(new Element($extension))) {
                $all[] = $aggregate;
            }
        }

        return $all;
    }

    /**
     * Retrieve the given extension by element name.
     *
     * @param Element $id
     *
     * @return Extension|null
     * @throws Exception
     */
    public function find(Element $id): ?Extension
    {
        try {
            return $this->assembler->assemble(
                new XMLExtensionSource(
                    $id,
                    $this->getExtensionManifest($id),
                    json_decode(
                        $this->http
                            ->request(
                                'GET',
                                "/repos/{$this->configuration->account}/{$id}/releases/latest",
                                [
                                    'base_uri' => $this->configuration->uri,
                                    'headers'  => [
                                        'Accept'        => 'application/vnd.github.v3+json',
                                        'Authorization' => "token {$this->configuration->token}",
                                    ],
                                ]
                            )
                            ->getBody()
                            ->getContents()
                    )
                )
            );
        } catch (GuzzleException | Exception $e) {
            return null;
        }
    }
}
