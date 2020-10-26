<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Application;

use Exception;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Domain\Extension;

final class ExtensionFinder
{
    /**
     * The extension repository implementation.
     *
     * @var ExtensionRepository
     */
    private ExtensionRepository $repository;

    /**
     * ExtensionFinder constructor.
     *
     * @param ExtensionRepository $repository
     */
    public function __construct(ExtensionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Return all the extensions.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return Extension[]
     * @throws Exception
     */
    public function all(int $limit = 100, int $offset = 0): array
    {
        return $this->repository->all($limit, $offset);
    }
}
