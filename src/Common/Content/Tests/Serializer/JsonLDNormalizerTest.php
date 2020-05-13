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

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Serializer\JsonLDNormalizer;
use Integrated\Common\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SymfonyNormalizerInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JsonLDNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var NormalizerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = $this->createMock(NormalizerInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(SymfonyNormalizerInterface::class, $this->getInstance());
    }

    /**
     * @dataProvider createNormalize
     */
    public function testNormalize($object, array $options, array $result, $expected)
    {
        $this->normalizer->expects($this->once())
            ->method('normalize')
            ->with($this->identicalTo($object), $options)
            ->willReturn($result);

        self::assertEquals($expected, $this->getInstance()->normalize($object, 'json-ld', $options));
    }

    public function createNormalize()
    {
        return [
            [
                $this->createMock(ContentInterface::class),
                [],
                ['array1'],
                ['@context' => 'http://schema.org', 'array1'],
            ],
            [
                $this->createMock(ContentInterface::class),
                ['key' => 'value'],
                ['array2'],
                ['@context' => 'http://schema.org', 'array2'],
            ],
            [
                $this->createMock(ContentInterface::class),
                [],
                ['array3', '@context' => 'http://example.org'],
                ['@context' => 'http://example.org', 'array3'],
            ],
            [
                $this->createMock(ContentInterface::class),
                ['key' => 'value'],
                [],
                null,
            ],
        ];
    }

    public function testSupportsNormalization()
    {
        $normalizer = $this->getInstance();

        $object = $this->createMock(ContentInterface::class);

        self::assertTrue($normalizer->supportsNormalization($object, 'json-ld'));
        self::assertFalse($normalizer->supportsNormalization($object, 'json'));

        $object = new stdClass();

        self::assertFalse($normalizer->supportsNormalization($object, 'json-ld'));
        self::assertFalse($normalizer->supportsNormalization($object, 'json'));
    }

    /**
     * @return JsonLDNormalizer
     */
    public function getInstance()
    {
        return new JsonLDNormalizer($this->normalizer);
    }
}
