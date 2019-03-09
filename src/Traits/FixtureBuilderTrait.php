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
use ScaleUpStack\EasyObject\Magic\FixtureBuilder;

trait FixtureBuilderTrait
{
    private $configuredProperties = [];

    public static function configure() : self
    {
        return new self();
    }

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
                FixtureBuilder::class,
            ]
        );
    }
}
