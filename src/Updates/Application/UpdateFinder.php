<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
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
     * @param  UpdateRepository  $repository
     */
    public function __construct(UpdateRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Return all the updates for the given element.
     *
     * @param  Element  $id
     * @param  int  $limit
     * @param  int  $offset
     *
     * @return Update[]
     */
    public function all(Element $id, int $limit = 100, int $offset = 0): array
    {
        return $this->repository->all($id, $limit, $offset);
    }
}
