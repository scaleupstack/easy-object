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
use ScaleUpStack\EasyObject\Tests\Resources\Traits\ForCommandTraitTesting;
use ScaleUpStack\Reflection\Reflection;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Traits\CommandTrait
 */
final class CommandTraitTest extends TestCase
{
    /**
     * @test
     * @covers ::__call()
     * @covers ::__callStatic()
     */
    public function it_uses_magic_methods_for_named_constructor_and_virtual_getters()
    {
        // given some required parameter
        $someProperty = 'some value';

        // when creating the event
        $command = ForCommandTraitTesting::instruct($someProperty);

        // then the property is accessible via the virtual getter
        $this->assertSame($someProperty, $command->someProperty());
    }

    /**
     * @test
     */
    public function it_cannot_be_constructed_via_new()
    {
        // the constructor must not be public
        $this->assertFalse(
            Reflection::methodOfClass(ForCommandTraitTesting::class, '__construct')->isPublic(),
            '__construct() must not be public'
        );
    }
}
