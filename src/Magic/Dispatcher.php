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

use ScaleUpStack\EasyObject\FeatureAnalyzers\VirtualMethods;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Factory;
use ScaleUpStack\Metadata\Metadata\VirtualMethodMetadata;

final class Dispatcher
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var CallHandler[]
     */
    private $callHandlers = [];

    private function __construct()
    {
    }

    private static function instance() : self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return mixed
     */
    public static function invoke(
        object $object,
        string $methodName,
        array $arguments,
        array $prioritizedCallHandlerClassNames
    )
    {
        return self::doInvocation($object, $methodName, $arguments, $prioritizedCallHandlerClassNames, false);
    }

    public static function invokeStatically(
        string $className,
        string $methodName,
        array $arguments,
        array $prioritizedCallHandlerClassNames
    )
    {
        return self::doInvocation($className, $methodName, $arguments, $prioritizedCallHandlerClassNames, true);
    }

    private static function doInvocation(
        $objectOrClassName,
        string $methodName,
        array $arguments,
        array $prioritizedCallHandlerClassNames,
        bool $isStatic
    )
    {
        $instance = self::instance();

        $className = $isStatic ? $objectOrClassName : get_class($objectOrClassName);
        $classMetadata = $instance->classMetadata($className);

        foreach ($prioritizedCallHandlerClassNames as $callHandlerData) {
            // prepare $callHandlerClassName and $options
            if (is_array($callHandlerData)) {
                $callHandlerClassName = $callHandlerData[0];
                $options = $callHandlerData[1];
            } else {
                $callHandlerClassName = $callHandlerData;
                $options = [];
            }

            // retrieve $callHandler
            if (! array_key_exists($callHandlerClassName, $instance->callHandlers)) {
                $instance->callHandlers[$callHandlerClassName] = new $callHandlerClassName();
            }

            $callHandler = $instance->callHandlers[$callHandlerClassName];

            // execute
            if ($callHandler->canHandle($methodName, $classMetadata, $options)) {
                $instance->assertGivenParametersMatchMethodSignature($methodName, $arguments, $classMetadata);

                if (! $isStatic) {
                    $return = $callHandler->execute($objectOrClassName, $methodName, $arguments, $classMetadata);
                } else {
                    $return = $callHandler->executeStatic($objectOrClassName, $methodName, $arguments, $classMetadata);
                }

                $instance->assertCorrectReturnType($objectOrClassName, $methodName, $return, $classMetadata);

                return $return;
            }
        }

        throw new \Error(
            sprintf(
                'Call to undefined method %s::%s()',
                $classMetadata->name,
                $methodName
            )
        );
    }

    private function classMetadata(string $className) : ClassMetadata
    {
        return Factory::getMetadataForClass($className)
            ->classMetadata[$className];
    }

    private function assertGivenParametersMatchMethodSignature(
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
    }

    /**
     * @param object|string $objectContext
     */
    private function assertCorrectReturnType(
        $objectContext,
        string $methodName,
        $returnValue,
        ClassMetadata $classMetadata
    )
    {
        /** @var VirtualMethodMetadata $virtualMethodMetadata */
        $virtualMethodMetadata = $classMetadata->features[VirtualMethods::FEATURES_KEY][$methodName];
        $returnType = $virtualMethodMetadata->returnType;

        if (! is_null($returnType->declaration())) {
            $isTypeValid = $returnType->validateVariable($returnValue, $objectContext);

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
