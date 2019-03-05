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

namespace ScaleUpStack\EasyObject\Metadata;

use Metadata\Driver\FileLocatorInterface;

final class FileLocator implements FileLocatorInterface
{
    public function findFileForClass(\ReflectionClass $class, string $extension) : ?string
    {
        return $class->getFileName();
    }
}
