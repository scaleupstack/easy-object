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

namespace ScaleUpStack\EasyObject\Tests\PhpUnit\Magic;

use ScaleUpStack\EasyObject\Magic\NamedConstructor;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForNamedConstructorTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Reflection\Reflection;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\NamedConstructor
 */
final class NamedConstructorTest extends TestCase
{
    /**
     * @test
     * @covers ::canHandle()
     * @covers ::requiresObjectContext()
     * @covers ::execute()
     */
    public function it_handles_and_executes_virtual_named_constructors()
    {
        // given a class name with a named constructor name, and required arguments
        $className = ClassForNamedConstructorTesting::class;
        $methodName = 'create';
        $stringValue = 'someString';
        $intValue = 42;

        // when calling the virtual named constructor
        $result = $className::$methodName($stringValue, $intValue);

        // then an instance of that class with the correct property values is returned
        $this->assertInstanceOf(ClassForNamedConstructorTesting::class, $result);
        $this->assertSame(
            $stringValue,
            Reflection::getPropertyValue($result, 'firstProperty')
        );
        $this->assertSame(
            $intValue,
            Reflection::getPropertyValue($result, 'secondProperty')
        );
    }
}
