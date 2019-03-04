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
    public $annotations;

    public $virtualPropertyMetadata = [];

    public $virtualMethodMetadata = [];

    public function __construct(string $name, Annotations $annotations)
    {
        parent::__construct($name);
        $this->setAnnotations($annotations);
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
            $annotations
        ) = unserialize($str);

        parent::unserialize($parent);

        $this->setAnnotations($annotations);
    }
}
