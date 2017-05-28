<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Content\Serializer;

use Integrated\Common\Content\Serializer\JsonLDEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JsonLDEncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(JsonEncoder::class, $this->getInstance());
    }

    public function testSupportsEncoding()
    {
        $encoder = $this->getInstance();

        self::assertTrue($encoder->supportsEncoding('json-ld'));
        self::assertFalse($encoder->supportsEncoding('json'));
    }

    public function testSupportsDecoding()
    {
        $encoder = $this->getInstance();

        self::assertTrue($encoder->supportsDecoding('json-ld'));
        self::assertFalse($encoder->supportsDecoding('json'));
    }

    /**
     * @return JsonLDEncoder
     */
    protected function getInstance()
    {
        return new JsonLDEncoder();
    }
}
