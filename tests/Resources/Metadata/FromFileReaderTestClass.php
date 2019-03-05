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

namespace ScaleUpStack\EasyObject\Tests\Resources\Metadata;

/**
 * @property-read $firstProperty
 * @method string secondProperty()
 * @method string getThirdProperty()
 */
class FromFileReaderTestClass
{
    /**
     * @var string
     */
    private $firstProperty = 'first value';

    /**
     * @var int
     */
    private $secondProperty = 42;

    private $thirdProperty = [];

    public function __call($name, $arguments)
    {
        return $this->$name;
    }

    public function __get($name)
    {
        return $this->$name;
    }
}
