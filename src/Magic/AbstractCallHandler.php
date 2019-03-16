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
use ScaleUpStack\Metadata\Metadata\VirtualMethodMetadata;

abstract class AbstractCallHandler implements CallHandler
{
    protected function checkForMethod(
        string $methodName,
        int $expectedNumberOfParameters,
        ClassMetadata $classMetadata
    )
    {
        $virtualMethods = $classMetadata->virtualMethods;

        // check for corresponding @method annotation
        if (! array_key_exists($methodName, $virtualMethods)) {
            return false;
        }

        // check for expected number of parameters
        $methodMetadata = $virtualMethods[$methodName];

        if ($expectedNumberOfParameters !== count($methodMetadata->paramters)) {
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

    protected function assertCanHandle(string $methodName, ClassMetadata $classMetadata)
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
    }

    protected function assertParameters(
        string $methodName,
        array $parameters,
        ClassMetadata $classMetadata
    )
    {
        $methodMetadata = $classMetadata->virtualMethods[$methodName];
        $expectedParameterCount = count($methodMetadata->paramters);

        $givenParametetersCount = count($parameters);

        if ($expectedParameterCount !== $givenParametetersCount) {
            throw new \ArgumentCountError(
                sprintf(
                    'Too %s arguments to function %s::%s(), %d passed and exactly %d expected',
                    $expectedParameterCount > $givenParametetersCount ? 'few' : 'many',
                    $classMetadata->name,
                    $methodName,
                    count($parameters),
                    $expectedParameterCount
                )
            );
        }

        // TODO: The types of the parameters need to be checked
    }

    protected function assertReturnType(
        object $object,
        string $methodName,
        $returnValue,
        ClassMetadata $classMetadata
    )
    {
        $virtualMethodMetadata = $classMetadata->virtualMethods[$methodName];
        $returnType = $virtualMethodMetadata->returnType;

        if (! is_null($returnType->declaration())) {
            $isTypeValid = $returnType->validateVariable($returnValue, $object);

            if (! $isTypeValid) {
                // TODO: Handle error on strict_types declaration of calling context (not file defining the class) :-/

                throw new \TypeError(
                    sprintf(
                        'Return value of %s::%s() must be of the type %s, %s returned',
                        $classMetadata->name,
                        $methodName,
                        $returnType->declaration(),
                        gettype($returnValue)
                    )
                );
            }
        }
    }
}
