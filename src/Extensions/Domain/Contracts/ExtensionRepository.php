<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Domain\Contracts;

use Exception;
use JEXUpdate\Extensions\Domain\Extension;
use JEXUpdate\Shared\Domain\ValueObjects\Element;

/**
 * Interface ExtensionRepository
 *
 * @package JEXUpdate\Extensions\Domain\Contracts
 */
interface ExtensionRepository
{
    /**
     * Retrieve all the watched extensions.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws Exception
     */
    public function all(int $limit = 100, int $offset = 0): array;
}
