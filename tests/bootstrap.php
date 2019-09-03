<?php declare(strict_types = 1);

namespace ScaleUpStack\EasyObject\Tests;

/**
 * This file is part of ScaleUpStack/EasyObject.
 *
 * For the full copyright and license information, please view the README.md and LICENSE.md files that were distributed
 * with this source code.
 *
 * @copyright 2019 - present ScaleUpVentures GmbH, https://www.scaleupventures.com
 * @link      https://github.com/scaleupstack/easy-object
 */

use ScaleUpStack\Metadata\Configuration;
use ScaleUpStack\Metadata\FeatureAnalyzers\VirtualMethods;

require_once __DIR__ . '/../vendor/autoload.php';

// register FeatureAnalyzers in ScaleUpStack\Metadata\Configuration
Configuration::registerFeatureAnalyzer(new VirtualMethods());
