<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Infrastructure\Persistence;

use Exception;
use GuzzleHttp\Client as HTTP;
use GuzzleHttp\Exception\GuzzleException;
use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Domain\ValueObjects\Version;
use JEXUpdate\Shared\Infrastructure\Persistence\GitHubConfiguration;
use JEXUpdate\Shared\Infrastructure\Persistence\GitHubRepository;
use JEXUpdate\Updates\Application\UpdateAssembler;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Domain\Update;
use JEXUpdate\Updates\Infrastructure\Sources\XMLUpdateSource;

/**
 * Class GitHubUpdateRepository
 *
 * @package JEXUpdate\Updates\Infrastructure\Persistence
 */
final class GitHubUpdateRepository extends GitHubRepository implements UpdateRepository
{
    /**
     * The Update assembler instance.
     *
     * @var UpdateAssembler
     */
    private UpdateAssembler $builder;

    /**
     * Client constructor.
     *
     * @param  GitHubConfiguration  $configuration
     * @param  HTTP  $http
     */
    public function __construct(GitHubConfiguration $configuration, HTTP $http)
    {
        parent::__construct($configuration, $http);
        $this->builder = new UpdateAssembler();
    }

    /**
     * Retrieve a collection of Updates for the given ExtensionId.
     *
     * @param  Element  $id
     * @param  int  $limit
     * @param  int  $offset
     *
     * @return array|Update[]
     * @throws Exception
     */
    public function all(Element $id, int $limit = 100, int $offset = 0): array
    {
        try {
            $manifest = $this->getExtensionManifest($id);
            $releases = json_decode(
                $this->http
                    ->request(
                        'GET',
                        "/repos/{$this->configuration->account}/{$id}/releases?"
                        .http_build_query(
                            [
                                'limit'  => $limit,
                                'offset' => $offset,
                            ]
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
                    ->getContents()
            );
        } catch (GuzzleException | Exception $e) {
            switch ($e->getCode()) {
                case 404:
                    return [];
                default:
                    throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }

        $updates = [];
        foreach ($releases as $release) {
            $updates[] = $this->builder->assemble(
                new XMLUpdateSource(
                    $id,
                    $manifest,
                    $release
                )
            );
        }

        return $updates;
    }

    /**
     * Find the exactly extension Update for the given ExtensionId and version.
     *
     * @param  Element  $id
     * @param  Version  $version
     *
     * @return Update|null
     * @throws Exception
     */
    public function find(Element $id, Version $version): ?Update
    {
        try {
            $manifest = $this->getExtensionManifest($id);
            $release = json_decode(
                $this->http
                    ->request(
                        'GET',
                        "/repos/{$this->configuration->account}/{$id}/releases/tags/v{$version}",
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
            );
        } catch (GuzzleException | Exception $e) {
            switch ($e->getCode()) {
                case 404:
                    return null;
                default:
                    throw new Exception($e->getMessage(), $e->getCode(), $e);
            }
        }

        return $this->builder->assemble(
            new XMLUpdateSource(
                $id,
                $manifest,
                $release
            )
        );
    }
}
