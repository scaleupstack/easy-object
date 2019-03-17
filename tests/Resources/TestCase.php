<?php declare(strict_types = 1);

/**
 * This file is part of ScaleUpStack/EasyObject.
 *
 * For the full copyright and license information, please view the README.md and LICENSE.md files that were distributed
 * with this source code.
 *
 * @copyright 2019 - present ScaleUpVentures GmbH, https://www.scaleupventures.com
 * @link      https://github.com/scaleupstack/easy-object
 */

namespace ScaleUpStack\EasyObject\Tests\Resources;

use ScaleUpStack\Metadata\Configuration;
use ScaleUpStack\Metadata\Metadata\ClassMetadata;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Copied from ScaleUpStack\Metadata\Generator\FromFileReader::analyzeRegisteredFeatures()
     */
    protected function analyzeFeatures(ClassMetadata $classMetadata)
    {
        $featureAnalyzers = Configuration::featureAnalyzers();

        foreach ($featureAnalyzers as $analyzer) {
            $key = $analyzer->name();
            $metadata =  $analyzer->extractMetadata($classMetadata);

            $classMetadata->features[$key] = $metadata;
        }
    }
}
