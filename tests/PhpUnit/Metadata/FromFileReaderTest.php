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

use Metadata\MetadataFactory;
use ScaleUpStack\Annotations\Annotation\MethodAnnotation;
use ScaleUpStack\Annotations\Annotation\PropertyReadAnnotation;
use ScaleUpStack\Annotations\Annotations;
use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\EasyObject\Metadata\FromFileReader;
use ScaleUpStack\EasyObject\Metadata\PropertyMetadata;
use ScaleUpStack\EasyObject\Tests\Resources\Metadata\ClassForTesting;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Metadata\FromFileReader
 */
final class FromFileReaderTest extends TestCase
{
    /**
     * @var MetadataFactory
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();

        $fileLocator = new \ScaleUpStack\EasyObject\Metadata\FileLocator();
        $this->factory = new MetadataFactory(
            new FromFileReader($fileLocator)
        );
    }

    /**
     * @test
     * @covers \ScaleUpStack\EasyObject\Metadata\FileLocator::findFileForClass()
     * @covers ::getExtension()
     * @covers ::loadMetadataFromFile()
     * @covers ::extractClassLevelMetadata()
     */
    public function it_analyzes_class_level_metadata()
    {
        // given a factory as provided via setUp() and a class name
        $className = ClassForTesting::class;

        // when retrieving the metadata
        $hierarchyMetadata = $this->factory->getMetadataForClass($className);
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $hierarchyMetadata->classMetadata[$className];

        // then the class name, the virtual methods, and the virtual properties are available
        $this->assertSame(
            $className,
            $classMetadata->name
        );

        $this->assertEquals(
            $classMetadata->virtualPropertyMetadata,
            [
                'firstProperty' => new PropertyReadAnnotation('property-read', '$firstProperty'),
            ]
        );

        $this->assertEquals(
            $classMetadata->virtualMethodMetadata,
            [
                 'secondProperty' => new MethodAnnotation('method', 'string secondProperty()'),
                 'getThirdProperty' => new MethodAnnotation('method', 'string getThirdProperty()'),
            ]
        );
    }

    /**
     * @test
     * @covers ::extractPropertyLevelMetadata()
     */
    public function it_analyzes_real_properties_metadata()
    {
        // given a factory as provided via setUp() and a class name
        $className = ClassForTesting::class;

        // when retrieving the metadata
        $hierarchyMetadata = $this->factory->getMetadataForClass($className);
        /** @var ClassMetadata $classMetadata */
        $classMetadata = $hierarchyMetadata->classMetadata[$className];

        // then the properties' metadata is available
        $firstPropertyAnnotations = new Annotations();
        $firstPropertyAnnotations->add('var', 'string', Annotations::CONTEXT_PROPERTY);
        $secondPropertyAnnotations = new Annotations();
        $secondPropertyAnnotations->add('var', 'int', Annotations::CONTEXT_PROPERTY);
        $thirdPropertyAnnotations = new Annotations();

        $this->assertEquals(
            [
                'firstProperty' => new PropertyMetadata($className, 'firstProperty', $firstPropertyAnnotations),
                'secondProperty' => new PropertyMetadata($className, 'secondProperty', $secondPropertyAnnotations),
                'thirdProperty' => new PropertyMetadata($className, 'thirdProperty', $thirdPropertyAnnotations),
            ],
            $classMetadata->propertyMetadata
        );
    }
}
