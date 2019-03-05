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
use ScaleUpStack\EasyObject\Magic\GetterCallHandler;
use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\EasyObject\Metadata\PropertyMetadata;
use ScaleUpStack\EasyObject\Tests\Resources\Magic\ClassForMagicTesting;
use ScaleUpStack\EasyObject\Tests\Resources\Metadata\ClassForTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Magic\GetterCallHandler
 */
final class GetterCallHandlerTest extends TestCase
{
    private function getClassMetadata(string $annotationTag, string $annotationArguments) : ClassMetadata
    {
        $annotations = new Annotations();
        $annotations->add($annotationTag, $annotationArguments, Annotations::CONTEXT_CLASS);

        $classMetadata = new ClassMetadata(ClassForMagicTesting::class, $annotations);

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
        // given a GetterCallHandler, and an object with its ClassMetadata
        $handler = new GetterCallHandler();
        // and an object's ClassMetadata with an annotation as provided by the test parameters
        $metadata = $this->getClassMetadata($annotationTag, $annotationArguments);

        // when checking if the handler is registered for a property
        $canHandle = $handler->canHandle($methodName, $metadata);

        // then the result is as expeced (as provided by the test method's parameter)
        $this->assertSame($expectedCanHandle, $canHandle);
    }
}

