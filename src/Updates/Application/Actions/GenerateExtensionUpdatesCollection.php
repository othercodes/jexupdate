<?php

declare(strict_types=1);

namespace JEXUpdate\Updates\Application\Actions;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Updates\Application\Contracts\UpdateSerializer;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Domain\Update;

/**
 * Class GetExtensionUpdatesAction
 *
 * @package JEXUpdate\Updates\Application\Actions
 */
final class GenerateExtensionUpdatesCollection
{
    /**
     * Update repository implementation.
     *
     * @var UpdateRepository
     */
    private UpdateRepository $repository;

    /**
     * The update serializer implementation.
     *
     * @var UpdateSerializer
     */
    private UpdateSerializer $serializer;

    /**
     * GenerateExtensionUpdatesXML constructor.
     *
     * @param UpdateRepository $repository
     * @param UpdateSerializer $serializer
     */
    public function __construct(UpdateRepository $repository, UpdateSerializer $serializer)
    {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * Get updates for the given extension.
     *
     * @param string $extension
     *
     * @return string
     */
    public function __invoke(string $extension): string
    {
        return $this->serializer->serialize(
            ...array_filter(
                $this->repository->all(new Element($extension)),
                fn(Update $update) => $update->downloads()->count() > 0
            )
        );
    }
}
