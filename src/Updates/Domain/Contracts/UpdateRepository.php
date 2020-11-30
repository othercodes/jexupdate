<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Domain\Contracts;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Updates\Domain\Update;

/**
 * Interface UpdateRepository
 *
 * @package JEXUpdate\Updates\Domain\Contracts
 */
interface UpdateRepository
{
    /**
     * Retrieve a collection of Updates for the given element.
     *
     * @param  Element  $id
     * @param  int  $limit
     * @param  int  $offset
     *
     * @return array|Update[]
     */
    public function all(Element $id, int $limit = 100, int $offset = 0): array;
}
