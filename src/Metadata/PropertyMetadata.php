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

use ScaleUpStack\Annotations\Annotations;

class PropertyMetadata extends \Metadata\PropertyMetadata
{
    /**
     * @var Annotations
     */
    public $annotations;

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(
            [
                parent::serialize(),
                $this->annotations,
            ]
        );
    }

    /**
     * @return void
     */
    public function unserialize($str)
    {
        list(
            $parent,
            $this->annotations
        ) = unserialize($str);

        parent::unserialize($parent);
    }
}
