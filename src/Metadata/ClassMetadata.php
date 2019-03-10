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

use ScaleUpStack\Annotations\Annotation\MethodAnnotation;
use ScaleUpStack\Annotations\Annotation\PropertyReadAnnotation;
use ScaleUpStack\Annotations\Annotations;

class ClassMetadata extends \Metadata\ClassMetadata
{
    public $namespace;

    /**
     * @var string[]
     *      <short class names> => <fully-qualified class name>
     */
    public $useStatements = [];

    public $annotations;

    public $virtualPropertyMetadata = [];

    public $virtualMethodMetadata = [];

    /**
     * @param string[] $useStatements
     */
    public function __construct(string $name, array $useStatements, Annotations $annotations)
    {
        parent::__construct($name);
        $this->setNamespace($name);
        $this->setUseStatements($useStatements);
        $this->setAnnotations($annotations);
    }

    private function setNamespace(string $className)
    {
        $parts = explode('\\', $className);
        array_pop($parts);
        $this->namespace = implode('\\', $parts);
    }

    private function setUseStatements(array $useStatements)
    {
        foreach ($useStatements as $useStatement) {
            $parts = explode(' ', $useStatement);

            if (1 === count($parts)) {
                $parts = explode('\\', $useStatement);
                $className = array_pop($parts);
                $this->useStatements[$className] = $useStatement;
            } else if (3 === count($parts)) {
                if ('as' !== $parts[1]) {
                    // TODO: throw
                }

                $this->useStatements[$parts[2]] = $parts[0];
            } else {
                // TODO: throw
            }
        }
    }

    private function setAnnotations(Annotations $annotations)
    {
        $this->annotations = $annotations;

        /** @var PropertyReadAnnotation $readPropertyAnnotation */
        foreach ($annotations->annotationsByTag('property-read') as $readPropertyAnnotation) {
            $this->virtualPropertyMetadata[$readPropertyAnnotation->propertyName()] = $readPropertyAnnotation;
        }

        /** @var MethodAnnotation $methodAnnotation */
        foreach ($annotations->annotationsByTag('method') as $methodAnnotation) {
            $this->virtualMethodMetadata[$methodAnnotation->methodName()] = $methodAnnotation;
        }
    }

    public function serialize() : string
    {
        return serialize(
            [
                parent::serialize(),
                $this->useStatements,
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
            $useStatements,
            $annotations
        ) = unserialize($str);

        parent::unserialize($parent);

        $this->setNamespace($this->name);
        $this->useStatements = $useStatements;
        $this->setAnnotations($annotations);
    }
}
