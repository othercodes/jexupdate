<?php

declare(strict_types=1);

namespace JEXUpdate\Shared\Infrastructure\Persistence;

/**
 * Class GitHubConfiguration
 *
 * @package JEXUpdate\Shared\Infrastructure\Persistence
 */
final class GitHubConfiguration
{
    public string $uri;
    public string $token;
    public string $account;
    public array $extensions;

    /**
     * GitHubRepositoryConfiguration constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->uri = $configuration['uri'];
        $this->token = $configuration['token'];
        $this->account = $configuration['account'];
        $this->extensions = $configuration['extensions'];
    }
}
