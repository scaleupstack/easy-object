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
use ScaleUpStack\Metadata\Factory;

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
        $instance = self::instance();

        $classMetadata = $instance->classMetadata(
            get_class($object)
        );

        foreach ($prioritizedCallHandlerClassNames as $callHandlerClassName) {
            if (! array_key_exists($callHandlerClassName, $instance->callHandlers)) {
                $instance->callHandlers[$callHandlerClassName] = new $callHandlerClassName();
            }

            $callHandler = $instance->callHandlers[$callHandlerClassName];

            if ($callHandler->canHandle($methodName, $classMetadata, [])) {
                return $callHandler->execute($object, $methodName, $arguments, $classMetadata);
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
}
