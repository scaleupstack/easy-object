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
use ScaleUpStack\EasyObject\Tests\Resources\Traits\EntityForTesting;

/**
 * @method int getSomeProperty()
 * @method bool getSomeValueDone()
 * @method self withSomeProperty(string $someProperty)
 * @method EntityForTesting build()
 */
final class ClassForAbstractCallHandlerTesting
{
    private $someProperty;

    private $getSomeValueDone;
}
