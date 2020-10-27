<?php

declare(strict_types=1);

namespace JEXUpdate\Tests\Unit\Extensions;

use JEXUpdate\Extensions\Application\Queries\GetExtensionCollection;
use JEXUpdate\Extensions\Domain\Contracts\ExtensionRepository;
use JEXUpdate\Tests\Unit\ExtensionMaker;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Class GetExtensionCollectionTest
 *
 * @package JEXUpdate\Tests\Unit\Extensions
 */
class GetExtensionCollectionTest extends MockeryTestCase
{
    use ExtensionMaker;

    public function testShouldGetExtensionCollection(): void
    {
        $repository = Mockery::mock(ExtensionRepository::class);
        $repository->shouldReceive('all')
            ->once()
            ->with(10, 0)
            ->andReturn([$this->buildExtension()]);


        $useCase = new GetExtensionCollection($repository);
        $collection = $useCase->execute();

        $this->assertIsArray($collection);
        $this->assertCount(1, $collection);
    }
}
