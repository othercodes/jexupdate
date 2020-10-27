<?php

declare(strict_types=1);

namespace JEXUpdate\Tests\Unit\Updates;

use JEXUpdate\Shared\Domain\ValueObjects\Element;
use JEXUpdate\Tests\Unit\UpdateMaker;
use JEXUpdate\Updates\Application\Queries\GetExtensionUpdatesCollection;
use JEXUpdate\Updates\Domain\Contracts\UpdateRepository;
use JEXUpdate\Updates\Domain\Exceptions\UpdateAssemblingFailure;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class GetExtensionUpdatesCollectionTest
 *
 * @package JEXUpdate\Tests\Unit\Updates
 */
class GetExtensionUpdatesCollectionTest extends MockeryTestCase
{
    use UpdateMaker;

    public function testShouldGetExtensionUpdatesCollection(): void
    {
        $extension = [
            'name'    => 'Hello World',
            'type'    => 'module',
            'element' => 'mod_helloworld',
        ];

        $repository = Mockery::mock(UpdateRepository::class);
        $repository->shouldReceive('all')
            ->once()
            ->with(Mockery::type(Element::class), 10, 0)
            ->andReturn([$this->buildUpdate($extension)]);


        $useCase = new GetExtensionUpdatesCollection($repository);
        $collection = $useCase->execute($extension['element']);

        $this->assertIsArray($collection);
        $this->assertCount(1, $collection);
    }
}
