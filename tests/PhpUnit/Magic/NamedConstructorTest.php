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
use ScaleUpStack\EasyObject\Magic\NamedConstructor;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForNamedConstructorTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Metadata\PropertyMetadata;
use ScaleUpStack\Reflection\Reflection;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\NamedConstructor
 */
final class NamedConstructorTest extends TestCase
{
    private function getClassMetadata(string $annotationArguments) : ClassMetadata
    {
        $annotations = new Annotations();
        $annotations->add('method', $annotationArguments, Annotations::CONTEXT_CLASS);

        $classMetadata = new ClassMetadata(ClassForNamedConstructorTesting::class, [], $annotations);
        $this->analyzeFeatures($classMetadata);

        $classMetadata->addPropertyMetadata(
            new PropertyMetadata(
                ClassForNamedConstructorTesting::class,
                'firstProperty',
                new Annotations()
            )
        );
        $classMetadata->addPropertyMetadata(
            new PropertyMetadata(
                ClassForNamedConstructorTesting::class,
                'secondProperty',
                new Annotations()
            )
        );

        return $classMetadata;
    }

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

    public function provides_data_of_valid_and_invalid_named_constructors() : array
    {
        return [
            ['static self create(string $firstProperty, int $secondProperty)', 'create', true],
            ['static self create(string $firstProperty, int $secondProperty)', 'other', false],             // expected method name would be 'other' not 'create'
            ['self create(string $firstProperty, int $secondProperty)', 'create', false],                   // not static
            ['static self create(string $firstProperty, int $secondProperty, $tooMuch)', 'create', false],  // wrong number of parameters
            ['static self unknown(string $firstProperty, int $secondProperty)', 'create', false],           // wrong method name
            ['static \DateTime create(string $firstProperty, int $secondProperty)', 'create', false],       // does not return self
            ['static self create(string $unknownProperty, int $secondProperty)', 'create', false],          // unknown property
        ];
    }

    /**
     * @test
     * @dataProvider provides_data_of_valid_and_invalid_named_constructors
     * @covers ::canHandle()
     */
    public function it_knows_if_it_can_handle_a_named_constructor(
        string $annotationArguments,
        string $supportedNamedConstructorName,
        bool $expectedCanHandle
    )
    {
        // given a NamedConstructor CallHandler
        $handler = new NamedConstructor();
        // and an object's ClassMetadata with a method annotation as provided by the test parameters
        $metadata = $this->getClassMetadata($annotationArguments);

        // when checking if the handler can create the object
        $canHandle = $handler->canHandle(
            'create',
            $metadata,
            [
                'methodName' => $supportedNamedConstructorName,
            ]
        );

        // then the result is as expected (as provided by the test method's parameter)
        $this->assertSame($expectedCanHandle, $canHandle);
    }
}
