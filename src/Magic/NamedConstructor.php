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

use ScaleUpStack\EasyObject\Assert;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Metadata\PropertyMetadata;
use ScaleUpStack\Reflection\Reflection;

final class NamedConstructor extends AbstractCallHandler
{
    const OPTION_KEY_METHOD_NAME = 'methodName';

    public function canHandle(string $methodName, ClassMetadata $classMetadata, array $options) : bool
    {
        Assert::keyExists(
            $options,
            self::OPTION_KEY_METHOD_NAME,
            sprintf(
                "The NamedConstructor CallHandler requires a '%s' option.",
                self::OPTION_KEY_METHOD_NAME
            )
        );
        Assert::string(
            $options[self::OPTION_KEY_METHOD_NAME],
            sprintf(
                "The '%s' option value of the NamedConstructor CallHandler must be a string.",
                self::OPTION_KEY_METHOD_NAME
            )
        );

        if ($methodName !== $options[self::OPTION_KEY_METHOD_NAME]) {
            return false;
        }

        $methodMetadata = $this->getMethodMetadata($methodName, $classMetadata);

        if (
            is_null($methodMetadata) ||
            ! $methodMetadata->isStatic ||
            'self' !== $methodMetadata->returnType->declaration()
        ) {
            return false;
        }

        if (
            ! $this->checkMethodsArgumentsCount(
                $methodName,
                count($classMetadata->propertyMetadata),
                $classMetadata
            )
        ) {
            return false;
        }


        foreach ($methodMetadata->parameters as $methodParameterName => $methodParameter) {
            if (! array_key_exists($methodParameterName, $classMetadata->propertyMetadata))
            {
                return false;
            }
        }

        return true;
    }

    public function requiresObjectContext() : bool
    {
        return false;
    }

    /**
     * @param $objectOrClassName
     *        NOTE: Intentionally the object type was left out for $objectOrClassName here.
     */
    public function execute($objectOrClassName, string $methodName, array $arguments, ClassMetadata $classMetadata)
    {
        $className = is_string($objectOrClassName) ? $objectOrClassName : get_class($objectOrClassName);

        $newObject = Reflection::classByName($className)
            ->newInstanceWithoutConstructor();

        $counter = 0;
        /** @var PropertyMetadata $propertyMetadata */
        foreach ($classMetadata->propertyMetadata as $propertyMetadata) {
            $this->setProperty(
                $newObject,
                $propertyMetadata->name,
                $arguments[$counter],
                $classMetadata
            );

            $counter++;
        }

        return $newObject;
    }
}
