<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Application;

use Exception;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Domain\Exceptions\ExtensionNotFound;
use JEXUpdate\Extensions\Domain\Extension;
use JEXUpdate\Shared\Domain\ValueObjects\Element;

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
     * Return the required extension.
     *
     * @param Element $id
     *
     * @return Extension
     * @throws ExtensionNotFound
     */
    public function find(Element $id): Extension
    {
        $extension = $this->repository->find($id);
        if (is_null($extension)) {
            throw new ExtensionNotFound("Unable to find {$id} extension");
        }

        return $extension;
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
