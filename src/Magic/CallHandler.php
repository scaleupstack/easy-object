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

use ScaleUpStack\Metadata\Metadata\ClassMetadata;

interface CallHandler
{
    /**
     * Validate if the method can be handled by the CallHandler. Check for example:
     *
     * - Is the virtual method in the class level annotations declared with the correct number of parameters?
     * - Does the corresponding property exist in the relevant object?
     *
     * @return bool
     */
    public function canHandle(string $methodName, ClassMetadata $classMetadata, array $options) : bool;

    /**
     * Executes a method on a given object.
     *
     * Do some assertions regarding the method, and execute the relevant code. For example:
     *
     * - Assert that the method can be handled be the CallHandler.
     * - Assert that the provided arguments are correct according to the method's signature.
     * - Execute the relevant code.
     * - Assert for the correct return type.
     * - Return the result.
     *
     * @return mixed
     */
    public function execute(object $object, string $methodName, array $arguments, ClassMetadata $classMetadata);
}
