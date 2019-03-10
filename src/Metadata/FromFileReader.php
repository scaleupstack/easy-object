<?php declare( strict_types = 1);

/**
 * This file is part of ScaleUpStack/EasyObject.
 *
 * For the full copyright and license information, please view the README.md and LICENSE.md files that were distributed
 * with this source code.
 *
 * @copyright 2019 - present ScaleUpVentures GmbH, https://www.scaleupventures.com
 * @link      https://github.com/scaleupstack/easy-object
 */

namespace ScaleUpStack\EasyObject\Metadata;

use Metadata\Driver\AbstractFileDriver;
use ScaleUpStack\Annotations\Annotations;
use ScaleUpStack\Annotations\DocBlockParser;

final class FromFileReader extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, string $file) : ?\Metadata\ClassMetadata
    {
        $docBlockParser = new DocBlockParser();

        $classMetadata = $this->extractClassLevelMetadata($class, $docBlockParser);
        $this->extractPropertyLevelMetaData($class, $docBlockParser, $classMetadata);

        return $classMetadata;
    }

    private function extractClassLevelMetadata(
        \ReflectionClass $reflectionClass,
        DocBlockParser $docBlockParser
    ) : ClassMetadata
    {
        $className = $reflectionClass->getName();

        $docBlock = $reflectionClass->getDocComment() ?: '';
        $annotations = $docBlockParser->parse($docBlock, Annotations::CONTEXT_CLASS);

        $classMetadata = new ClassMetadata($className, [], $annotations);

        return $classMetadata;
    }

    private function extractPropertyLevelMetadata(
        \ReflectionClass $reflectionClass,
        DocBlockParser $docBlockParser,
        ClassMetadata $classMetadata
    ) : void
    {
        foreach ($reflectionClass->getProperties() as $property) {
            $docBlock = $property->getDocComment() ?: '';

            $classMetadata->addPropertyMetadata(
                new PropertyMetadata(
                    $classMetadata->name,
                    $property->getName(),
                    $docBlockParser->parse($docBlock, Annotations::CONTEXT_PROPERTY)
                )
            );
        }
    }

    protected function getExtension() : string
    {
        return 'php';
    }
}
