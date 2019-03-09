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

use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\EasyObject\Tests\Resources\Traits\EntityForTesting;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Traits\EntityTrait
 */
final class EntityTraitTest extends TestCase
{
    /**
     * @test
     * @covers ::__call()
     */
    public function it_supports_virtual_getters()
    {
        // given an entity
        $entity = new EntityForTesting();

        // when calling the virtual getter
        $value = $entity->someProperty();

        // then the value of the property is returned
        $this->assertSame(42, $value);
    }
}
