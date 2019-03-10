<?php

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

use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\Reflection\Reflection;

final class VirtualGetter extends AbstractCallHandler
{
    public function canHandle(string $methodName, ClassMetadata $classMetadata) : bool
    {
        return (
            $this->checkForMethod($methodName, 0, $classMetadata) &&
            null !== $this->propertyName($methodName, 'get', false, $classMetadata)
        );
    }

    public function execute(object $object, string $methodName, array $arguments, ClassMetadata $classMetadata)
    {
        $this->assertCanHandle($methodName, $classMetadata);
        $this->assertParameters($methodName, $arguments, $classMetadata);

        $propertyName = $this->propertyName($methodName, 'get', false, $classMetadata);
        $propertyValue = Reflection::getPropertyValue($object, $propertyName);

        $this->assertReturnType($object, $methodName, $propertyValue, $classMetadata);

        return $propertyValue;
    }
}