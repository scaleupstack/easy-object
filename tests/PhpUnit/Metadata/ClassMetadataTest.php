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

namespace ScaleUpStack\EasyObject\Tests\PhpUnit\Metadata;

use ScaleUpStack\EasyObject\Metadata\ClassMetadata;
use ScaleUpStack\EasyObject\Tests\Resources\TestCase;

/**
 * @coversDefaultClass \ScaleUpStack\EasyObject\Metadata\ClassMetadata
 */
final class ClassMetadataTest extends TestCase
{
    /**
     * @test
     * @covers ::serialize()
     * @covers ::unserialize()
     */
    public function it_can_be_serialized_and_unserialized()
    {
        // given a ClassMetadata instance
        $metadata = new ClassMetadata(ClassMetadata::class);

        // when serializing and unserializing the metadata
        $unserializedMetadata = unserialize(serialize($metadata));

        // then the both instances are equal
        $this->assertEquals($metadata, $unserializedMetadata);
    }
}

