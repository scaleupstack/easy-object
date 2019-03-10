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

use ScaleUpStack\Annotations\Annotation\MethodAnnotation;
use ScaleUpStack\Annotations\Annotation\PropertyReadAnnotation;
use ScaleUpStack\Annotations\Annotations;
use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\EasyObject\Tests\Resources\Metadata\ClassForTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Metadata\ClassMetadata
 */
final class ClassMetadataTest extends TestCase
{
    /**
     * @test
     * @covers ::__construct()
     * @covers ::setNamespace()
     * @covers ::setUseStatements()
     * @covers ::setAnnotations()
     */
    public function it_stores_metadata_for_virtual_properties_and_methods()
    {
        // given a class name
        $className = ClassForTesting::class;
        // and some use statements
        $useStatements = [
            'ScaleUpStack\Annotations\Annotation\MethodAnnotation',
            'Metadata\ClassMetadata as BaseClassMetadata',
        ];
        // and some Annotations with a PropertyReadAnnotation and a MethodAnnotation
        $annotations = new Annotations();
        $annotations->add('property-read', 'string $someProperty', Annotations::CONTEXT_CLASS);
        $annotations->add('method', 'int getSomeProperty()', Annotations::CONTEXT_CLASS);

        // when creating the ClassMetadata
        $classMetadata = new ClassMetadata($className, $useStatements, $annotations);

        // then the namespace is stored
        $this->assertSame('ScaleUpStack\EasyObject\Tests\Resources\Metadata', $classMetadata->namespace);
        // and the use statements are compiled
        $this->assertSame(
            [
                'MethodAnnotation' => 'ScaleUpStack\Annotations\Annotation\MethodAnnotation',
                'BaseClassMetadata' => 'Metadata\ClassMetadata',
            ],
            $classMetadata->useStatements
        );
        // and the annotations are stored while the virtual properties and methods are available directly
        $this->assertSame($annotations, $classMetadata->annotations);
        $this->assertEquals(
            [
                'someProperty' => new PropertyReadAnnotation('property-read', 'string $someProperty'),
            ],
            $classMetadata->virtualPropertyMetadata
        );

        $this->assertEquals(
            [
                'getSomeProperty' => new MethodAnnotation('method', 'int getSomeProperty()'),
            ],
            $classMetadata->virtualMethodMetadata
        );
    }

    /**
     * @test
     * @covers ::serialize()
     * @covers ::unserialize()
     */
    public function it_can_be_serialized_and_unserialized()
    {
        // given a ClassMetadata instance with virtual properties and methods
        $annotations = new Annotations();
        $annotations->add('property-read', 'string $someProperty', Annotations::CONTEXT_CLASS);
        $annotations->add('method', 'int getSomeProperty()', Annotations::CONTEXT_CLASS);
        $metadata = new ClassMetadata(
            ClassMetadata::class,
            [
                Annotations::class,
            ],
            $annotations
        );

        // when serializing and unserializing the metadata
        $unserializedMetadata = unserialize(serialize($metadata));

        // then the both instances are equal
        $this->assertEquals($metadata, $unserializedMetadata);
    }
}
