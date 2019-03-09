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

use ScaleUpStack\EasyObject\Magic\AbstractCallHandler;
use ScaleUpStack\EasyObject\Metadata\Factory;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForAbstractCallHandlerTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\EasyObject\Tests\Resources\Traits\EntityForTesting;
use ScaleUpStack\Reflection\Reflection;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\AbstractCallHandler
 */
final class AbstractCallHandlerTest extends TestCase
{
    /**
     * @var AbstractCallHandler
     */
    private $callHandler;

    private $classMetadata;

    protected function setUp()
    {
        $this->callHandler = $this->getMockForAbstractClass(AbstractCallHandler::class);
        $this->classMetadata = Factory::getMetadataForClass(ClassForAbstractCallHandlerTesting::class)
            ->classMetadata[ClassForAbstractCallHandlerTesting::class];
    }

    public function provides_virtual_method_names_with_parameter_count() : array
    {
        return [
            ['getSomeProperty', 0, true],
            ['getSomeProperty', 1, false],  // wrong parameter count
            ['someProperty', 0, false],     // unknown method
            ['getSomeValueDone', 0, true],  // corresponding property starts with "get"
            ['build', 0, true],             // no corresponding property
        ];
    }

    /**
     * @test
     * @dataProvider provides_virtual_method_names_with_parameter_count
     * @covers ::checkForMethod()
     */
    public function it_checks_if_virtual_method_matches(
        string $methodName,
        int $expectedParameterCount,
        bool $expectedResult
    )
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and a method name, and expected number of parameters both as provided via test parameters

        // when checking for the method
        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'checkForMethod')
            ->invoke(
                $this->callHandler,
                $methodName,
                $expectedParameterCount,
                $this->classMetadata
            );

        // then the result is as expected
        $this->assertSame($expectedResult, $result);
    }

    public function provides_virtual_method_names_with_corresponding_properties() : array
    {
        return [
            ['getSomeProperty', 'get', false, 'someProperty'],
            ['someProperty', 'get', false, 'someProperty'],
            ['withSomeProperty', 'with', true, 'someProperty'],
            ['getSomeValueDone', 'get', false, 'getSomeValueDone'],
            ['getSomeValueDone', 'get', true, null],                    // no corresponding property
            ['build', '', true, null],                                  // no correspoding property
        ];
    }

    /**
     * @test
     * @dataProvider provides_virtual_method_names_with_corresponding_properties
     * @covers ::propertyName()
     */
    public function it_returns_the_corresponding_property_name_to_a_method_name(
        string $methodName,
        string $methodPrefix,
        bool $isPrefixRequired,
        ?string $expectedPropertyResult
    )
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and a method name, a method prefix, and if the prefix is required

        // when requesting the property name
        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'propertyName')
            ->invoke(
                $this->callHandler,
                $methodName,
                $methodPrefix,
                $isPrefixRequired,
                $this->classMetadata
            );

        // then the result is as expected
        $this->assertSame($expectedPropertyResult, $result);
    }

    /**
     * @test
     * @covers ::assertCanHandle()
     */
    public function it_returns_void_on_assertCanHandle_when_it_can_handle_a_method()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and the AbstractCallHandler can handle the method
        $this->callHandler->method('canHandle')->willReturn(true);

        // when asserting that a method can be handled
        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'assertCanHandle')
            ->invoke(
                $this->callHandler,
                'getSomeProperty',
                $this->classMetadata
            );

        // then the $result was null
        $this->assertNull($result);
    }

    /**
     * @test
     * @covers ::assertCanHandle()
     */
    public function it_throws_an_exception_on_assertCanHandle_when_it_cannot_handle_a_method()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and the AbstractCallHandler can't handle the method
        $this->callHandler->method('canHandle')->willReturn(false);

        // when asserting that a method can be handled
        // then an exception is thrown
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            sprintf(
                'Call to undefined method %s::%s()',
                ClassForAbstractCallHandlerTesting::class,
                'someUnknownMethod'
            )
        );

        Reflection::methodOfClass(AbstractCallHandler::class, 'assertCanHandle')
            ->invoke(
                $this->callHandler,
                'someUnknownMethod',
                $this->classMetadata
            );
    }

    /**
     * @test
     * @covers ::assertParameters()
     */
    public function it_returns_void_on_assertParameters_when_number_of_parameters_is_valid()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()

        // when asserting the parameters
        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'assertParameters')
            ->invoke(
                $this->callHandler,
                'withSomeProperty',
                [
                    'some value'
                ],
                $this->classMetadata
            );

        // then the $result is null
        $this->assertNull($result);
    }

    /**
     * @test
     * @covers ::assertParameters()
     */
    public function it_throws_an_exception_on_assertParameters_when_number_of_parameters_is_invalid()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()

        // when asserting the parameters
        // then an exception is thrown
        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Too few arguments to function %s::%s(), 0 passed and exactly 1 expected',
                ClassForAbstractCallHandlerTesting::class,
                'withSomeProperty'
            )
        );

        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'assertParameters')
            ->invoke(
                $this->callHandler,
                'withSomeProperty',
                [],
                $this->classMetadata
            );
    }

    /**
     * @test
     * @covers ::assertReturnType()
     */
    public function it_returns_void_on_assertReturnType_when_return_value_is_of_correct_type()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and some object, a method name, and a return value
        $object = new ClassForAbstractCallHandlerTesting();
        $methodName = 'getSomeProperty';
        $returnValue = 42;

        // when asserting the return type of the return value
        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'assertReturnType')
            ->invoke(
                $this->callHandler,
                $object,
                $methodName,
                $returnValue,
                $this->classMetadata
            );

        // then the $result is null
        $this->assertNull($result);
    }

    /**
     * @test
     * @covers ::assertReturnType()
     */
    public function it_throws_an_exception_on_assertReturnType_when_return_value_is_of_invalid_type()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and some object, a method name, and some return value
        $object = new ClassForAbstractCallHandlerTesting();
        $methodName = 'getSomeProperty';
        $returnValue = 'not an int';

        // when asserting the return type of the return value
        // then an exception is thrown
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Return value of %s::%s() must be of the type int, string returned',
                ClassForAbstractCallHandlerTesting::class,
                $methodName
            )
        );

        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'assertReturnType')
            ->invoke(
                $this->callHandler,
                $object,
                $methodName,
                $returnValue,
                $this->classMetadata
            );
    }
}
