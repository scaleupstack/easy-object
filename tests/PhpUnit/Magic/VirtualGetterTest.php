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
use ScaleUpStack\EasyObject\Magic\VirtualGetter;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForMagicTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Metadata\PropertyMetadata;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\VirtualGetter
 */
final class VirtualGetterTest extends TestCase
{
    private function getClassMetadata(string $annotationTag, string $annotationArguments) : ClassMetadata
    {
        $annotations = new Annotations();
        $annotations->add($annotationTag, $annotationArguments, Annotations::CONTEXT_CLASS);

        $classMetadata = new ClassMetadata(ClassForMagicTesting::class, [], $annotations);
        $this->analyzeFeatures($classMetadata);

        $classMetadata->addPropertyMetadata(
            new PropertyMetadata(
                ClassForMagicTesting::class,
                'someProperty',
                new Annotations()
            )
        );

        return $classMetadata;
    }

    public function provides_data_of_registered_and_unregistered_virtual_getters()
    {
        return [
            ['someProperty', 'property-read', 'int $someProperty', false],      // not @method but @property-read
            ['someProperty', 'method', 'int someProperty()', true],             // @method
            ['getSomeProperty', 'method', 'int someProperty()', false],         // no @method with 'get' prefix
            ['someProperty', 'method', 'someProperty($someParameter)', false],  // @method has parameter and thus is not getter
            ['getSomeProperty', 'method', 'int getSomeProperty()', true],       // @method with 'get' prefix
            ['unknownProperty', 'method', 'unknownProperty()', false],          // @method but no corresponding property
        ];
    }

    /**
     * @test
     * @dataProvider provides_data_of_registered_and_unregistered_virtual_getters
     * @covers ::canHandle()
     */
    public function it_knows_if_it_can_handle_a_virtual_method(
        string $methodName,
        string $annotationTag,
        string $annotationArguments,
        bool $expectedCanHandle
    )
    {
        // given a VirtualGetter call handler, and an object with its ClassMetadata
        $handler = new VirtualGetter();
        // and an object's ClassMetadata with an annotation as provided by the test parameters
        $metadata = $this->getClassMetadata($annotationTag, $annotationArguments);

        // when checking if the handler is registered for a property
        $canHandle = $handler->canHandle($methodName, $metadata, []);

        // then the result is as expeced (as provided by the test method's parameter)
        $this->assertSame($expectedCanHandle, $canHandle);
    }

    public function provides_valid_return_types()
    {
        return [
            ['int '],
            [''],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider provides_valid_return_types
     * @covers ::execute()
     */
    public function it_executes_a_virtual_getter($returnType)
    {
        // given a VirtualGetter call handler, an object, and the object's ClassMetadata
        $handler = new VirtualGetter();
        $object = new ClassForMagicTesting();
        $metadata = $this->getClassMetadata(
            'method',
            $returnType . 'getSomeProperty()'
        );

        // when executing the magic method
        $result = $handler->execute($object, 'getSomeProperty', [], $metadata);

        // then the result is the properties value
        $this->assertSame(42, $result);
    }
}

