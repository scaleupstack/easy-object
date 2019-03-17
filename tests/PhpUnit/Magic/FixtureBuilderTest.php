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

use ScaleUpStack\Annotations\Annotations;
use ScaleUpStack\EasyObject\Assert;
use ScaleUpStack\EasyObject\FeatureAnalyzers\VirtualMethods;
use ScaleUpStack\EasyObject\Magic\FixtureBuilder;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForFixtureBuilderTesting;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForMagicTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Metadata\Factory;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Metadata\DataTypeMetadata;
use ScaleUpStack\Reflection\Reflection;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\FixtureBuilder
 */
final class FixtureBuilderTest extends TestCase
{
    public function provides_method_names_and_if_they_can_be_handled()
    {
        return [
            ['build', true],
            ['withSomeProperty', true],
            ['withUnknownProperty', false],
            ['withPropertyWithWrongBuilderMethod', false] // there is no parameter given in method signature
        ];
    }

    /**
     * @test
     * @dataProvider provides_method_names_and_if_they_can_be_handled
     * @covers ::canHandle()
     * @covers ::getMetadataOfClassToBeBuilt()
     */
    public function it_knows_if_it_can_handle_a_method_call(string $methodName, bool $expectedCanHandle)
    {
        // given a FixtureBuilder call handler, and some ClassMetadata
        $callHandler = new FixtureBuilder();
        $classMetadata = Factory::getMetadataForClass(ClassForFixtureBuilderTesting::class)
            ->classMetadata[ClassForFixtureBuilderTesting::class];

        // when checking if the handler is registered for a method
        $canHandle = $callHandler->canHandle($methodName, $classMetadata);

        // then the result is as expeced
        $this->assertSame($expectedCanHandle, $canHandle);
    }

    /**
     * @test
     * @covers ::canHandle()
     */
    public function it_cannot_handle_a_with_method_if_build_method_is_missing()
    {
        // given a FixtureBilder call handler
        $callHandler = new FixtureBuilder();
        // and some ClassMetadata
        /** @var ClassMetadata $classMetadata */
        $classMetadata = Factory::getMetadataForClass(ClassForFixtureBuilderTesting::class)
            ->classMetadata[ClassForFixtureBuilderTesting::class];
        // and a method name that would be supported (if build() is available)
        $methodName = 'withSomeProperty';
        Assert::true($callHandler->canHandle($methodName, $classMetadata));
        // but the ClassMetadata has no build() method
        /** @var ClassMetadata $classMetadata */
        $classMetadata = clone $classMetadata;
        unset($classMetadata->features[VirtualMethods::FEATURES_KEY]['build']);

        // when checking if the handler can handle a with method
        $result = $callHandler->canHandle($methodName, $classMetadata);

        // then the result is false
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::canHandle()
     * @covers ::getMetadataOfClassToBeBuilt()
     */
    public function it_cannot_handle_a_with_method_if_builder_method_has_no_return_type()
    {
        // given a FixtureBilder call handler
        $callHandler = new FixtureBuilder();
        // and some ClassMetadata
        /** @var ClassMetadata $classMetadata */
        $classMetadata = Factory::getMetadataForClass(ClassForFixtureBuilderTesting::class)
            ->classMetadata[ClassForFixtureBuilderTesting::class];
        // and a method name that would be supported (if build() would have a return type)
        $methodName = 'withSomeProperty';
        Assert::true($callHandler->canHandle($methodName, $classMetadata));
        // but the ClassMetadata's build() method has no return type
        $classMetadata = clone $classMetadata;
        $classMetadata->features[VirtualMethods::FEATURES_KEY]['build'] =
            clone $classMetadata->features[VirtualMethods::FEATURES_KEY]['build'];
        Reflection::setPropertyValue(
            $classMetadata->features[VirtualMethods::FEATURES_KEY]['build'],
            'returnType',
            new DataTypeMetadata(null)
        );

        // when checking if the handler can handle a with method
        $result = $callHandler->canHandle($methodName, $classMetadata);

        // then the result is false
        $this->assertFalse($result);
    }

    /**
     * @test
     * @covers ::execute()
     * @covers ::executeWith()
     */
    public function it_handles_with_methods()
    {
        // given a FixtureBuilder call handler, some FixtureBuilder object,
        // and a method name, a new value, and some ClassMetadata
        $callHandler = new FixtureBuilder();
        $fixtureBuilder = new ClassForFixtureBuilderTesting();
        $methodName = 'withSomeProperty';
        $newValue = 'configuredValue';
        $classMetadata = Factory::getMetadataForClass(ClassForFixtureBuilderTesting::class)
            ->classMetadata[ClassForFixtureBuilderTesting::class];

        // when configuring the value of a property
        $callHandler->execute(
            $fixtureBuilder,
            $methodName,
            [$newValue],
            $classMetadata
        );

        // then the fixture builder object holds the configured value
        $this->assertSame(
            $newValue,
            Reflection::getPropertyValue($fixtureBuilder, 'configuredProperties')['someProperty']
        );
    }

    /**
     * @test
     * @covers ::execute()
     * @covers ::executeBuild()
     */
    public function it_handles_build_method()
    {
        // given a FixtureBuilder call handler, and some ClassMetadata
        $callHandler = new FixtureBuilder();
        $classMetadata = Factory::getMetadataForClass(ClassForFixtureBuilderTesting::class)
            ->classMetadata[ClassForFixtureBuilderTesting::class];
        // and some configured FixtureBuilder object,
        $fixtureBuilder = new ClassForFixtureBuilderTesting();
        $callHandler->execute(
            $fixtureBuilder,
            'withSomeProperty',
            ['not the default value'],
            $classMetadata
        );
        $callHandler->execute(
            $fixtureBuilder,
            'withSomePropertyWithoutDefaultValue',
            ['now you have a value'],
            $classMetadata
        );

        // when the class is built
        /** @var ClassForMagicTesting $object */
        $object = $callHandler->execute(
            $fixtureBuilder,
            'build',
            [],
            $classMetadata
        );

        // then the properties are as configured or as defined via @example
        $this->assertSame(
            'not the default value',
            Reflection::getPropertyValue($object, 'someProperty')
        );
        $this->assertSame(
            'now you have a value',
            Reflection::getPropertyValue($object, 'somePropertyWithoutDefaultValue')
        );
        $this->assertSame(
            'some value from @example annotation',
            Reflection::getPropertyValue($object, 'somePropertyWithExampleAnnotation')
        );
        $this->assertInstanceOf(
            Annotations::class,
            Reflection::getPropertyValue($object, 'propertyWithWrongBuilderMethod')
        );
    }
}
