<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application\Queries;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Updates\Application\UpdateDTOAssembler;
use JEXUpdate\Updates\Application\UpdateFinder;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Domain\Update;

/**
 * Class GetExtensionUpdatesCollection
 *
 * @package JEXUpdate\Updates\Application\Queries
 */
final class GetExtensionUpdatesCollection
{
    /**
     * Update finder instance.
     *
     * @var UpdateFinder
     */
    private UpdateFinder $finder;

    /**
     * The extension update  DTO assembler instance.
     *
     * @var UpdateDTOAssembler
     */
    private UpdateDTOAssembler $assembler;

    /**
     * GetExtensionUpdatesCollection constructor.
     *
     * @param  UpdateRepository  $repository
     */
    public function __construct(UpdateRepository $repository)
    {
        $this->finder = new UpdateFinder($repository);
        $this->assembler = new UpdateDTOAssembler();
    }

    /**
     * Get updates for the given extension.
     *
     * @param  string  $extension
     * @param  int  $limit
     * @param  int  $offset
     *
     * @return array
     */
    public function execute(string $extension, int $limit = 10, int $offset = 0): array
    {
        $extension = new Element(current(explode('.', $extension, 2)));

        return array_map(
            fn(Update $update) => $this->assembler->assemble($update),
            $this->finder->all($extension, $limit, $offset)
        );
    }
}
