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

/**
 * @method self withSomeProperty($someProperty)
 * @method self withSomePropertyWithoutDefaultValue($newValue)
 * @method self withUnknownProperty($unknownProperty)
 * @method self withPropertyWithWrongBuilderMethod()
 * @method ClassForMagicTesting build()
 */
final class ClassForFixtureBuilderTesting
{
    private $configuredProperties = [];
}
