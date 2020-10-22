<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Shared\Domain\ValueObjects\Version;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Domain\Exceptions\UpdateNotFound;
use JEXUpdate\Updates\Domain\Update;

/**
 * Class UpdateFinder
 *
 * @package JEXUpdate\Updates\Application
 */
final class UpdateFinder
{
    /**
     * The extension update repository implementation.
     *
     * @var UpdateRepository
     */
    private UpdateRepository $repository;

    /**
     * UpdateFinder constructor.
     *
     * @param UpdateRepository $repository
     */
    public function __construct(UpdateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Return the required update.
     *
     * @param Element $id
     * @param Version $version
     *
     * @return Update
     * @throws UpdateNotFound
     */
    public function find(Element $id, Version $version): Update
    {
        $update = $this->repository->find($id, $version);
        if (is_null($update)) {
            throw new UpdateNotFound("Unable to find {$id} v{$version} update");
        }

        return $update;
    }

    /**
     * Return all the updates for the given element.
     *
     * @param Element $id
     * @param int     $limit
     * @param int     $offset
     *
     * @return Update[]
     */
    public function all(Element $id, int $limit = 100, int $offset = 0): array
    {
        return $this->repository->all($id, $limit, $offset);
    }
}
