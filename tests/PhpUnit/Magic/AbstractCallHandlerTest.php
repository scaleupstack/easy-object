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
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForAbstractCallHandlerTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Metadata\Factory;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
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

    /**
     * @var ClassMetadata
     */
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
     * @covers ::checkMethodsArgumentsCount()
     * @covers ::getMethodMetadata()
     */
    public function it_checks_if_argument_count_of_virtual_method_matches(
        string $methodName,
        int $expectedParameterCount,
        bool $expectedResult
    )
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp()
        // and a method name, and expected number of parameters both as provided via test parameters

        // when checking for the method
        $result = Reflection::methodOfClass(AbstractCallHandler::class, 'checkMethodsArgumentsCount')
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
        // and a method name, a method prefix, and if the prefix is required as provided by the test's parameters

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
     * @covers ::setProperty()
     */
    public function it_sets_the_value_of_an_object_when_type_matches()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp(),
        // and some object, a property name, and a new value
        $object = new ClassForAbstractCallHandlerTesting();
        $propertyName = 'someProperty';
        $newValue = 'newValue';
        // and a method name, a method prefix, and if the prefix is required as provided by the test's parameters

        // when requesting the property name
        Reflection::methodOfClass(AbstractCallHandler::class, 'setProperty')
            ->invoke(
                $this->callHandler,
                $object,
                $propertyName,
                $newValue,
                $this->classMetadata
            );

        // then the property of the object is set
        $this->assertSame(
            $newValue,
            Reflection::getPropertyValue($object, 'someProperty')
        );
    }

    /**
     * @test
     * @covers ::setProperty()
     */
    public function it_throws_an_exception_when_setting_the_property_of_an_object_with_an_invalid_type()
    {
        // given a mocked AbstractCallHandler, and ClassMetadata of some object as provided in setUp(),
        // and some object, a property name, and an invalid value
        $object = new ClassForAbstractCallHandlerTesting();
        $propertyName = 'someProperty';
        $invalidValue = 42;

        // when requesting the property name
        // then an Exception is thrown
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                "Value for property %s::\$%s must be of the type string, integer given",
                ClassForAbstractCallHandlerTesting::class,
                $propertyName
            )
        );

        Reflection::methodOfClass(AbstractCallHandler::class, 'setProperty')
            ->invoke(
                $this->callHandler,
                $object,
                $propertyName,
                $invalidValue,
                $this->classMetadata
            );
    }
}
