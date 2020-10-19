<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application\Contracts;

use JEXUpdate\Updates\Domain\Update;

/**
 * Interface UpdateSerializer
 *
 * @package JEXUpdate\Updates\Application\Contracts
 */
interface UpdateSerializer
{
    /**
     * Serialize the given Update Aggregate.
     *
     * @param Update ...$update
     *
     * @return mixed
     */
    public function serialize(Update ...$update): string;
}
