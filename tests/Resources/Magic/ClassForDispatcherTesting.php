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

namespace ScaleUpStack\EasyObject\Tests\Resources\Magic;

/**
 * @method static self myFactoryMethod(int $someProperty, string $otherProperty)
 * @method int getSomeProperty()
 */
final class ClassForDispatcherTesting
{
    private $someProperty = 42;

    private $otherProperty;
}
