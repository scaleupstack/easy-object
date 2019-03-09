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

namespace ScaleUpStack\EasyObject\Tests\Resources\Traits;

use ScaleUpStack\EasyObject\Traits\FixtureBuilderTrait;
use ScaleUpStack\EasyObject\Tests\Resources\Traits\EntityForTesting;

/**
 * @method self withSomeProperty(int $someProperty)
 * @method ScaleUpStack\EasyObject\Tests\Resources\Traits\EntityForTesting build()
 */
final class FixtureBuilderForTesting
{
    use FixtureBuilderTrait;
}
