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
    public function it_stores_metadata_for_virtual_methods()
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
        $annotations->add('property-read', 'ClassForTesting $someProperty', Annotations::CONTEXT_CLASS);
        $annotations->add('method', 'BaseClassMetadata[] getSomeProperty()', Annotations::CONTEXT_CLASS);

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
        // and the annotations are stored while the virtual methods are available directly
        $this->assertSame($annotations, $classMetadata->annotations);

        $this->assertEquals(
            [
                'getSomeProperty' => new MethodAnnotation('method', 'BaseClassMetadata[] getSomeProperty()'),
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

    public function provides_short_and_fully_qualified_data_type_specifications() : array
    {
        return [
            ['int', 'int'],
            ['bool', 'bool'],
            ['\DateTime', 'DateTime'],
            ['ClassMetadata', 'ScaleUpStack\EasyObject\Metadata\ClassMetadata'],
            ['BaseClassMetadata', 'Metadata\ClassMetadata'],
            ['ClassForTesting', 'ScaleUpStack\EasyObject\Tests\Resources\Metadata\ClassForTesting'],

            ['int[]', 'int[]'],
            ['\DateTime[]', 'DateTime[]'],
            ['ClassMetadata[]', 'ScaleUpStack\EasyObject\Metadata\ClassMetadata[]'],

            ['int[]|\DateTime', 'int[]|DateTime'],
        ];
    }

    /**
     * @test
     * @dataProvider provides_short_and_fully_qualified_data_type_specifications
     * @covers ::fullyQualifiedDataTypeSpecification()
     */
    public function it_transforms_a_data_type_specification_into_a_fully_qualified_specification(
        string $shortSpecification,
        string $expectedLongSpecification
    )
    {
        // given a ClassMetadata
        $classMetadata = new ClassMetadata(
            ClassForTesting::class,
            [
                'ScaleUpStack\EasyObject\Metadata\ClassMetadata',
                'Metadata\ClassMetadata as BaseClassMetadata',
            ],
            new Annotations()
        );
        // and a data type specification as provided by the test's parameter

        // when transforming the short data type specification
        $longSpecification = $classMetadata->fullyQualifiedDataTypeSpecification($shortSpecification);

        // then the specification was transformed to a fully qualified data type specification
        $this->assertSame($expectedLongSpecification, $longSpecification);
    }
}

