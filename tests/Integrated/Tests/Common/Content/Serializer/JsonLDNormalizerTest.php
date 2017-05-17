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

use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Serializer\JsonLDNormalizer;
use Integrated\Common\Normalizer\NormalizerInterface;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SymfonyNormalizerInterface;

use stdClass as Object;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class JsonLDNormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NormalizerInterface  | \PHPUnit_Framework_MockObject_MockObject
     */
    private $normalizer;

    protected function setUp()
    {
        $this->normalizer = $this->getMock(NormalizerInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(SymfonyNormalizerInterface::class, $this->getInstance());
    }

    /**
     * @dataProvider createNormalize
     */
    public function testNormalize($object, array $options, array $result, $exspected)
    {
        $this->normalizer->expects($this->once())
            ->method('normalize')
            ->with($this->identicalTo($object), $options)
            ->willReturn($result);

        self::assertEquals($exspected, $this->getInstance()->normalize($object, 'json-ld', $options));
    }

    public function createNormalize()
    {
        return [
            [
                $this->getMock(ContentInterface::class),
                [],
                ['array1'],
                ['@context' => 'http://schema.org', 'array1']
            ],
            [
                $this->getMock(ContentInterface::class),
                ['key' => 'value'],
                ['array2'],
                ['@context' => 'http://schema.org', 'array2']
            ],
            [
                $this->getMock(ContentInterface::class),
                [],
                ['array3', '@context' => 'http://example.org'],
                ['@context' => 'http://example.org', 'array3']
            ],
            [
                $this->getMock(ContentInterface::class),
                ['key' => 'value'],
                [],
                null
            ],
        ];
    }

    public function testSupportsNormalization()
    {
        $normalizer = $this->getInstance();

        $object = $this->getMock(ContentInterface::class);

        self::assertTrue($normalizer->supportsNormalization($object, 'json-ld'));
        self::assertFalse($normalizer->supportsNormalization($object, 'json'));

        $object = new Object();

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
