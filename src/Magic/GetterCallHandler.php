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
        $propertyName = $methodName;
        if ('get' === substr($methodName, 0, 3)) {
            $propertyName = lcfirst(
                substr($methodName, 3)
            );
        }

        if (! array_key_exists($propertyName, $classMetadata->propertyMetadata)) {
            return false;
        }

        // virtual getters must have no parameters
        /** @var MethodAnnotation $methodMetadata */
        $methodMetadata = $virtualMethods[$methodName];

        return ([] === $methodMetadata->parameters());
    }

    public function execute(ClassMetadata $classMetadata, string $methodName, array $arguments)
    {
        // TODO: Implement execute() method.
    }
}
