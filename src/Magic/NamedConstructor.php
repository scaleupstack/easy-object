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

namespace ScaleUpStack\EasyObject\Magic;

use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Metadata\PropertyMetadata;
use ScaleUpStack\Reflection\Reflection;

final class NamedConstructor extends AbstractCallHandler
{
    public function canHandle(string $methodName, ClassMetadata $classMetadata, array $options) : bool
    {
        return true;
    }

    public function execute(object $object, string $methodName, array $arguments, ClassMetadata $classMetadata)
    {
        return $this->executeStatic(
            get_class($object),
            $methodName,
            $arguments,
            $classMetadata
        );
    }

    public function executeStatic(string $className, string $methodName, array $arguments, ClassMetadata $classMetadata)
    {
        $newObject = Reflection::classByName($className)
            ->newInstanceWithoutConstructor();

        $counter = 0;
        /** @var PropertyMetadata $propertyMetadata */
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            Reflection::setPropertyValue(
                $newObject,
                $propertyMetadata->name,
                $arguments[$counter]
            );

            $counter++;
        }

        return $newObject;
    }
}
