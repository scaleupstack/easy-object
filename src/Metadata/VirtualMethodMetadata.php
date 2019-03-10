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

namespace ScaleUpStack\EasyObject\Metadata;

use ScaleUpStack\EasyObject\Assert;

final class VirtualMethodMetadata
{
    public $class;

    public $name;

    public $paramters = [];

    public $returnType;

    /**
     * @param DataTypeMetadata[] $parameters
     *        <parameterName> => <DataTypeMetadata>
     */
    public function __construct(string $class, string $name, array $parameters, DataTypeMetadata $returnType)
    {
        Assert::allIsInstanceOf(
            $parameters,
            DataTypeMetadata::class,
            '$parameters must be an array of DataTypeMetadata'
        );
        Assert::allString(
            array_keys($parameters),
            'The array keys of $parameters must be the parameter names.'
        );

        $this->class = $class;
        $this->name = $name;
        $this->paramters = $parameters;
        $this->returnType = $returnType;
    }
}
