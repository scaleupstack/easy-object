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

namespace ScaleUpStack\EasyObject\Tests\Resources\Magic;
use ScaleUpStack\EasyObject\Magic\Dispatcher;
use ScaleUpStack\EasyObject\Magic\NamedConstructor;

/**
 * @method static self create(string $firstProperty, int $secondProperty)
 */
final class ClassForNamedConstructorTesting
{
    private $firstProperty;

    private $secondProperty;

    /**
     * NOTES:
     *
     * - This method is required for automatic fall-back to __callStatic()
     *
     * - For test coverage, a call to Dispatcher is required. In practice, a simpler implementation would be sufficient
     *   when no CallHandlers are handled by __call():
     *
     *   `return self::__callStatic($methodName, $arguments);`
     */
    public function __call($methodName, $arguments)
    {
        return Dispatcher::invoke(
            $this,
            $methodName,
            $arguments,
            []
        );
    }

    public static function __callStatic($methodName, $arguments)
    {
        return Dispatcher::invokeStatically(
            self::class,
            $methodName,
            $arguments,
            [
                [
                    NamedConstructor::class,
                    [
                        'methodName' => 'create',
                    ],
                ]
            ]
        );
    }
}
