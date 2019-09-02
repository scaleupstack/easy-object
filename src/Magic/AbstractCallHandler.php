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

use ScaleUpStack\EasyObject\FeatureAnalyzers\VirtualMethods;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;

abstract class AbstractCallHandler implements CallHandler
{
    public function executeStatic(
        string $className,
        string $methodName,
        array $arguments,
        ClassMetadata $classMetadata
    )
    {
        throw new \Error("Calling non-static method when not in object context.");
    }

    protected function checkForMethod(
        string $methodName,
        int $expectedNumberOfParameters,
        ClassMetadata $classMetadata
    )
    {
        $virtualMethods = $classMetadata->features[VirtualMethods::FEATURES_KEY];

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

    protected function assertGivenParametersMatchMethodSignature(
        string $methodName,
        array $parameters,
        ClassMetadata $classMetadata
    )
    {
        $methodMetadata = $classMetadata->features[VirtualMethods::FEATURES_KEY][$methodName];
        $expectedParameterCount = count($methodMetadata->paramters);

        $givenParametersCount = count($parameters);

        if ($expectedParameterCount !== $givenParametersCount) {
            throw new \ArgumentCountError(
                sprintf(
                    'Too %s arguments to function %s::%s(), %d passed and exactly %d expected',
                    $expectedParameterCount > $givenParametersCount ? 'few' : 'many',
                    $classMetadata->name,
                    $methodName,
                    count($parameters),
                    $expectedParameterCount
                )
            );
        }

        // TODO: The types of the parameters need to be checked
    }
}
