<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content\Tests\Serializer;

use Integrated\Common\Content\Serializer\JsonLBNullNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JsonLBNullNormalizerTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->getInstance());
    }

    public function testNormalize()
    {
        self::assertNull($this->getInstance()->normalize(new stdClass(), 'json-ld'));
        self::assertNull($this->getInstance()->normalize(null, 'json-ld'));
    }

    public function testSupportsNormalization()
    {
        $normalizer = $this->getInstance();

        self::assertTrue($normalizer->supportsNormalization(new stdClass(), 'json-ld'));
        self::assertFalse($normalizer->supportsNormalization(new stdClass(), 'json'));
    }

    /**
     * @return JsonLBNullNormalizer
     */
    public function getInstance()
    {
        return new JsonLBNullNormalizer();
    }
}
