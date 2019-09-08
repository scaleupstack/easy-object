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

namespace ScaleUpStack\EasyObject\Traits;

use ScaleUpStack\EasyObject\Magic\Dispatcher;
use ScaleUpStack\EasyObject\Magic\NamedConstructor;
use ScaleUpStack\EasyObject\Magic\VirtualGetter;

trait CommandTrait
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public function __call(string $method, array $parameters)
    {
        return Dispatcher::invoke(
            $this,
            $method,
            $parameters,
            [
                VirtualGetter::class,
            ]
        );
    }

    public static function __callStatic(string $method, array $parameters)
    {
        return Dispatcher::invokeStatically(
            self::class,
            $method,
            $parameters,
            [
                [
                    NamedConstructor::class,
                    ['methodName' => 'instruct'],
                ],
            ]
        );
    }
}
