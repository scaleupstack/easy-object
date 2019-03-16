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

namespace ScaleUpStack\EasyObject\Tests\PhpUnit\Metadata;

use ScaleUpStack\EasyObject\Metadata\VirtualMethodMetadata;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;
use ScaleUpStack\Metadata\Metadata\DataTypeMetadata;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Metadata\VirtualMethodMetadata
 */
final class VirtualMethodMetadataTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct()
     */
    public function it_can_be_created()
    {
        // given a class name, a method name, some parameters, and a return type
        $className = 'SomeClassName';
        $methodName = 'SomeMethodName';
        $parameters = [
            'firstParameter' => new DataTypeMetadata('int'),
            'secondParameter' => new DataTypeMetadata(null),
        ];
        $returnType = new DataTypeMetadata('\DateTime');

        // when constructing the VirtualMethodMetadata
        $metadata = new VirtualMethodMetadata($className, $methodName, $parameters, $returnType);

        // then the values are set
        $this->assertSame($className, $metadata->class);
        $this->assertSame($methodName, $metadata->name);
        $this->assertSame($parameters, $metadata->paramters);
        $this->assertSame($returnType, $metadata->returnType);
    }
}
