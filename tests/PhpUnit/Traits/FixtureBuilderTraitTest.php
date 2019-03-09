<?php declare(strict_types = 1);

/**
 * This file is part of ScaleUpStack/EasyObject
 *
 * For the full copyright and license information, please view the README.md and LICENSE.md files that were distributed
 * with this source code.
 *
 * @copyright 2019 - present ScaleUpVentures GmbH, https://www.scaleupventures.com
 * @link      https://github.com/scaleupstack/easy-object
 */

namespace ScaleUpStack\EasyObject\Tests\PhpUnit\Traits;

use ScaleUpStack\EasyObject\Tests\Resources\Traits\FixtureBuilderForTesting;
use ScaleUpStack\EasyObject\Traits\FixtureBuilderTrait;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Traits\FixtureBuilderTrait
 */
final class FixtureBuilderTraitTest extends TestCase
{
    /**
     * @test
     * @covers ::configure()
     * @covers ::__construct()
     * @covers ::__call()
     */
    public function it_supports_virtual_methods_for_with_configuration_and_build()
    {
        // given a fixure builder
        $fixtureBuilder = FixtureBuilderForTesting::configure();

        // when configuring the property, and building
        $entity = $fixtureBuilder->withSomeProperty(200)
            ->build();

        // then the property is set correctly
        $this->assertSame(200, $entity->someProperty());
    }
}
