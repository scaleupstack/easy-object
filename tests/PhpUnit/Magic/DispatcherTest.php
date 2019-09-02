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

use ScaleUpStack\EasyObject\Magic\Dispatcher;
use ScaleUpStack\EasyObject\Magic\NamedConstructor;
use ScaleUpStack\EasyObject\Magic\VirtualGetter;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForDispatcherTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Reflection\Reflection;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\Dispatcher
 */
final class DispatcherTest extends TestCase
{
    /**
     * @test
     * @covers ::instance()
     * @covers ::__construct()
     * @covers ::classMetadata()
     * @covers ::invoke()
     * @covers ::doInvocation()
     * @covers ::assertReturnType()
     */
    public function it_invokes_a_virtual_non_static_method_on_some_object_via_some_call_handler()
    {
        // given an object, and a list of supported call handlers
        $object = new ClassForDispatcherTesting();
        $supportedCallHandlers = [
            VirtualGetter::class
        ];

        // when invoking an allowed method
        $result = Dispatcher::invoke(
            $object,
            'getSomeProperty',
            [],
            $supportedCallHandlers
        );

        // then the result is as expected
        $this->assertSame(42, $result);
    }

    /**
     * @test
     * @covers ::invokeStatically()
     * @covers ::doInvocation()
     */
    public function it_invokes_a_virtual_static_method_on_some_object_via_some_call_handler()
    {
        // given a class name, and a list of supported call handlers
        $className = ClassForDispatcherTesting::class;
        $supportedCallHandlers = [
            [
                NamedConstructor::class,
                [
                    'methodName' => 'myFactoryMethod',
                ]
            ],
        ];

        // when invoking an allowed method
        $result = Dispatcher::invokeStatically(
            $className,
            'myFactoryMethod',
            [
                17,
            ],
            $supportedCallHandlers
        );

        // then the result is as expected
        $this->assertInstanceOf(ClassForDispatcherTesting::class, $result);
        $this->assertSame(
            17,
            Reflection::getPropertyValue($result, 'someProperty')
        );
    }

    /**
     * @test
     * @covers ::assertReturnType()
     */
    public function it_throws_an_exception_when_the_return_type_is_invalid()
    {
        // given an object, and a list of supported call handlers
        $object = new ClassForDispatcherTesting();
        $supportedCallHandlers = [
            VirtualGetter::class
        ];
        // and an invalid type of the property (according to the virtual getter)
        Reflection::setPropertyValue($object, 'someProperty', 'not an int as expected');

        // when invoking an allowed method that would return a wrong return type
        // then an exception is thrown
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Return value of %s::%s() must be of the type %s, %s returned',
                ClassForDispatcherTesting::class,
                'getSomeProperty',
                'int',
                'string'
            )
        );

        $result = Dispatcher::invoke(
            $object,
            'getSomeProperty',
            [],
            $supportedCallHandlers
        );
    }

    /**
     * @test
     * @covers ::doInvocation()
     */
    public function it_throws_an_exception_if_no_call_handler_can_handle_the_method()
    {
        // given an object, and a list of supported call handlers
        $object = new ClassForDispatcherTesting();
        $supportedCallHandlers = [
            VirtualGetter::class
        ];

        // when invoking an unsupported method
        // then an exception is thrown
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined method ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForDispatcherTesting::notSupported()'
        );

        Dispatcher::invoke(
            $object,
            'notSupported',
            [],
            $supportedCallHandlers
        );
    }
}

