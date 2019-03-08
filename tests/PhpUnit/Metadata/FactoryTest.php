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

use Metadata\ClassHierarchyMetadata;
use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\EasyObject\Metadata\Factory;
use ScaleUpStack\EasyObject\Tests\Resources\Metadata\ClassForTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Metadata\Factory
 */
final class FactoryTest extends TestCase
{
    /**
     * @test
     * @covers ::getMetadataForClass()
     * @covers ::metadataFactory()
     */
    public function it_retrieves_class_metadata_for_a_classname()
    {
        // given a class name
        $className = ClassForTesting::class;

        // when getting the metadata for a class
        $hierarchyMetadata = Factory::getMetadataForClass($className);

        // then the ClassHierarchyMetadata of the class is returned
        $this->assertInstanceOf(ClassHierarchyMetadata::class, $hierarchyMetadata);
        $this->assertSame(
            $className,
            $hierarchyMetadata->classMetadata[$className]->name
        );
    }
}
