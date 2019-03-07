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

use ScaleUpStack\Annotations\Annotation\MethodAnnotation;
use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\EasyObject\Metadata\DataTypeMetadata;
use ScaleUpStack\Reflection\Reflection;

class GetterCallHandler implements CallHandler
{
    public function canHandle(string $methodName, ClassMetadata $classMetadata) : bool
    {
        $virtualMethods = $classMetadata->virtualMethodMetadata;

        // virtual getters must have a corresponding @method annotation
        if (! array_key_exists($methodName, $virtualMethods))
        {
            return false;
        }

        // virtual getters can have a 'get' prefix, and must have a corresponding property
        $propertyName = $this->propertyName($methodName);

        if (! array_key_exists($propertyName, $classMetadata->propertyMetadata)) {
            return false;
        }

        // virtual getters must have no parameters
        /** @var MethodAnnotation $methodMetadata */
        $methodMetadata = $virtualMethods[$methodName];

        return ([] === $methodMetadata->parameters());
    }

    public function execute(object $object, string $methodName, array $arguments, ClassMetadata $classMetadata)
    {
        if (! $this->canHandle($methodName, $classMetadata)) {
            throw new \Error(
                sprintf(
                    'Call to undefined method %s::%s()',
                    $classMetadata->name,
                    $methodName
                )
            );
        }

        if (0 !== count($arguments)) {
            throw new \ArgumentCountError(
                sprintf(
                    'Too many arguments to function %s::%s(), %s passed and exactly 0 expected',
                    $classMetadata->name,
                    $methodName,
                    count($arguments)
                )
            );
        }

        $propertyName = $this->propertyName($methodName);
        $propertyValue = Reflection::getPropertyValue($object, $propertyName);

        /** @var MethodAnnotation $virtualMethodAnnotation */
        $virtualMethodAnnotation = $classMetadata->virtualMethodMetadata[$methodName];
        $returnType = $virtualMethodAnnotation->returnType();

        if (! is_null($returnType)) {
            $dataType = new DataTypeMetadata($returnType);
            $isReturnValueValid = $dataType->validateVariable($propertyValue, $object);

            if (! $isReturnValueValid) {
                // TODO: Handle error on strict_types declaration of calling context (not file defining the class) :-/

                throw new \TypeError(
                    $errorMessage = sprintf(
                        'Return value of %s::%s() must be of the type %s, %s returned',
                        $classMetadata->name,
                        $methodName,
                        $returnType,
                        gettype($propertyValue)
                    )
                );
            }
        }

        return $propertyValue;
    }

    private function propertyName(string $methodName) : string
    {
        $propertyName = $methodName;

        if ('get' === substr($methodName, 0, 3)) {
            $propertyName = lcfirst(
                substr($methodName, 3)
            );
        }

        return $propertyName;
    }
}
