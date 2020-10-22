<?php

declare(strict_types=1);

namespace JEXUpdate\Extensions\Application\Queries;

use Exception;
use JEXUpdate\Extensions\Application\ExtensionDTOAssembler;
use JEXUpdate\Extensions\Application\ExtensionFinder;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Extensions\Domain\Extension;

/**
 * Class GetExtensionCollection
 *
 * @package JEXUpdate\Extensions\Application\Queries
 */
final class GetExtensionCollection
{
    /**
     * The extension finder instance.
     *
     * @var ExtensionFinder
     */
    private ExtensionFinder $finder;

    /**
     * The extension DTO assembler instance.
     *
     * @var ExtensionDTOAssembler
     */
    private ExtensionDTOAssembler $assembler;

    /**
     * GetExtensionCollection constructor.
     *
     * @param ExtensionRepository $repository
     */
    public function __construct(ExtensionRepository $repository)
    {
        $this->finder = new ExtensionFinder($repository);
        $this->assembler = new ExtensionDTOAssembler();
    }

    /**
     * Execute the use case.
     *
     * @param int $limit
     * @param int $offset
     *
     * @return array
     * @throws Exception
     */
    public function execute(int $limit = 10, int $offset = 0): array
    {
        return array_map(
            fn(Extension $extension) => $this->assembler->assemble($extension),
            $this->finder->all($limit, $offset)
        );
    }
}
