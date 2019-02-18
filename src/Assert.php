<?php

/**
 * This file is part of ScaleUpStack/EasyObject.
 *
 * For the full copyright and license information, please view the README.md and LICENSE.md files that were distributed
 * with this source code.
 *
 * @copyright 2019 - present ScaleUpVentures GmbH, https://www.scaleupventures.com
 * @link      https://github.com/scaleupstack/easy-object
 */

namespace ScaleUpStack\EasyObject;

class Assert extends \ScaleUpStack\Assert\Assert
{
    protected static $assertionClassName = InvalidArgumentException::class;
}
