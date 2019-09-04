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

use ScaleUpStack\Metadata\FeatureAnalyzers\TypedProperties;
use ScaleUpStack\Metadata\FeatureAnalyzers\TypedPropertyMetadata;
use ScaleUpStack\Metadata\FeatureAnalyzers\VirtualMethods;
use ScaleUpStack\Metadata\FeatureAnalyzers\VirtualMethodMetadata;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Reflection\Reflection;

abstract class AbstractCallHandler implements CallHandler
{
    /* default implementation of CallHandler methods */

    public function requiresObjectContext() : bool
    {
        return true;
    }

    /* helper methods */

    protected function getMethodMetadata(string $methodName, ClassMetadata $classMetadata) : ?VirtualMethodMetadata
    {
        $virtualMethods = $classMetadata->features[VirtualMethods::class];

        // check for corresponding @method annotation
        if (! array_key_exists($methodName, $virtualMethods)) {
            return null;
        }

        // check for expected number of parameters
        return $virtualMethods[$methodName];
    }

    protected function checkMethodsArgumentsCount(
        string $methodName,
        int $expectedNumberOfParameters,
        ClassMetadata $classMetadata
    ) : bool
    {
        $methodMetadata = $this->getMethodMetadata($methodName, $classMetadata);

        if (is_null($methodMetadata)) {
            return false;
        }

        if ($expectedNumberOfParameters !== count($methodMetadata->parameters)) {
            return false;
        }

        return true;
    }

    protected function propertyName(
        string $methodName,
        string $methodPrefix,
        bool $isPrefixRequired,
        ClassMetadata $classMetadata
    ) : ?string
    {
        $propertyName = $methodName;

        // if prefix is not required, check if method name equals a property name
        if (
            ! $isPrefixRequired &&
            array_key_exists($propertyName, $classMetadata->propertyMetadata)
        ) {
            return $propertyName;
        }

        // remove prefix
        $prefixLength = strlen($methodPrefix);
        if ($methodPrefix === substr($methodName, 0, $prefixLength)) {
            $propertyName = lcfirst(
                substr($methodName, $prefixLength)
            );
        }

        // check if method name equals a property name
        if (array_key_exists($propertyName, $classMetadata->propertyMetadata)) {
            return $propertyName;
        }

        return null;
    }

    protected function setProperty(object $object, string $propertyName, $value, ClassMetadata $classMetadata)
    {
        /** @var TypedPropertyMetadata $typedPropertyMetadata */
        $typedPropertyMetadata = $classMetadata->features[TypedProperties::class][$propertyName];
        $isValid = $typedPropertyMetadata->docBlockDataTypeMetadata()->validateVariable($value, $object);

        if (! $isValid) {
            throw new \TypeError(
                sprintf(
                    "Value for property %s::\$%s must be of the type %s, %s given",
                    get_class($object),
                    $propertyName,
                    $typedPropertyMetadata->docBlockDataTypeMetadata()->declaration(),
                    gettype($value)
                )
            );
        }

        Reflection::setPropertyValue($object, $propertyName, $value);
    }
}
