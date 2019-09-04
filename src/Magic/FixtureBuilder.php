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

namespace ScaleUpStack\EasyObject\Magic;

use ScaleUpStack\Annotations\Annotation\UnknownAnnotation;
use ScaleUpStack\Metadata\Factory;
use ScaleUpStack\Metadata\FeatureAnalyzers\VirtualMethods;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;
use ScaleUpStack\Metadata\Metadata\PropertyMetadata;
use ScaleUpStack\Reflection\Reflection;

final class FixtureBuilder extends AbstractCallHandler
{
    private const CONFIGURED_PROPERTIES = 'configuredProperties';

    public function canHandle(string $methodName, ClassMetadata $classMetadata, array $options) : bool
    {
        // a build() method is always required
        if (! $this->checkMethodsArgumentsCount('build', 0, $classMetadata)) {
            return false;
        }

        // is build() method?
        if ('build' === $methodName) {
            return true;
        }

        // otherwise, a with<SomeProperty>() method:

        // check if method is declared
        if (! $this->checkMethodsArgumentsCount($methodName, 1, $classMetadata))  {
            return false;
        }

        // check if property is defined in class to be built
        $toBeBuiltClassMetadata = $this->getMetadataOfClassToBeBuilt($classMetadata);
        if (is_null($toBeBuiltClassMetadata)) {
            return false;
        }

        $propertyName = $this->propertyName($methodName, 'with', true, $toBeBuiltClassMetadata);

        return ! is_null($propertyName);
    }

    public function execute(object $object, string $methodName, array $arguments, ClassMetadata $classMetadata)
    {
        $toBeBuiltClassMetadata = $this->getMetadataOfClassToBeBuilt($classMetadata);

        if ('build' === $methodName) {
            return $this->executeBuild($object, $toBeBuiltClassMetadata);
        }

        $this->executeWith($object, $methodName, $arguments, $toBeBuiltClassMetadata);
        return $object;
    }

    private function executeBuild(object $object, ClassMetadata $toBeBuildClassMetadata)
    {
        $newObject = Reflection::classByName($toBeBuildClassMetadata->name)
                ->newInstanceWithoutConstructor();

        $configuredProperties = Reflection::getPropertyValue($object, self::CONFIGURED_PROPERTIES);

        /** @var PropertyMetadata $propertyMetadata */
        foreach ($toBeBuildClassMetadata->propertyMetadata as $propertyMetadata) {
            $propertyName = $propertyMetadata->name;

            if (array_key_exists($propertyName, $configuredProperties)) {
                $value = $configuredProperties[$propertyName];
            } else {
                $exampleAnnotations = $propertyMetadata->annotations->annotationsByTag('example');

                // TODO: throw if no example or too many or ...

                // TODO: ExampleAnnotation
                /** @var UnknownAnnotation $exampleAnnotation */
                $exampleAnnotation = $exampleAnnotations[0];
                $phpTemplate = <<<EVAL_CODE
namespace %s {
%s
    return %s;
}
EVAL_CODE;

                $useStatements = [];
                foreach ($toBeBuildClassMetadata->useStatements as $alias => $fullyQualified) {
                    $useStatements[] = sprintf(
                        "    use %s as %s;\n",
                        $fullyQualified,
                        $alias
                    );
                }
                $phpString = sprintf(
                    $phpTemplate,
                    $toBeBuildClassMetadata->namespace,
                    implode("", $useStatements),
                    $exampleAnnotation->arguments()
                );

                $value = eval($phpString);
            }

            $this->setProperty($newObject, $propertyName, $value, $propertyMetadata);
        }

        return $newObject;
    }

    private function executeWith(
        object $object,
        string $methodName,
        array $arguments,
        ClassMetadata $toBeBuiltClassMetadata
    )
    {
        $propertyName = $this->propertyName($methodName, 'with', true, $toBeBuiltClassMetadata);
        $propertyValue = $arguments[0];

        // TODO: check data type

        $configuredProperties = Reflection::getPropertyValue($object, self::CONFIGURED_PROPERTIES);
        $configuredProperties[$propertyName] = $propertyValue;
        Reflection::setPropertyValue($object, self::CONFIGURED_PROPERTIES, $configuredProperties);
    }

    private function getMetadataOfClassToBeBuilt(ClassMetadata $classMetadata) : ?ClassMetadata
    {
        // get name of class to be built
        $virtualBuildMethod = $classMetadata->features[VirtualMethods::class]['build'];
        $buildClassName = $virtualBuildMethod->returnType->declaration();

        if (is_null($buildClassName)) {
            return null;
        }

        // get ClassMetadata of class to be built
        /** @var ClassMetadata $buildClassMetadata */
        return Factory::getMetadataForClass($buildClassName)
            ->classMetadata[$buildClassName];
    }
}
