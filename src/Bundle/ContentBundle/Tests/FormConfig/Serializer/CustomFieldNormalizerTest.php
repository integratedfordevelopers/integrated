<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig\Serializer;

use Integrated\Bundle\ContentBundle\Document\FormConfig\Embedded\Field\CustomField;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\FormConfigFieldTransformer;
use Integrated\Bundle\ContentBundle\FormConfig\Serializer\CustomFieldNormalizer;
use Integrated\Bundle\ContentBundle\Twig\Extension\FormConfigExtension;
use Integrated\Common\FormConfig\FormConfigFieldInterface;
use stdClass as Object;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CustomFieldNormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigFieldTransformer | \PHPUnit\Framework\MockObject\MockObject
     */
    private $transformer;

    /**
     * @var FormConfigExtension | \PHPUnit\Framework\MockObject\MockObject
     */
    private $extension;

    protected function setUp()
    {
        $this->transformer = $this->createMock(FormConfigFieldTransformer::class);
        $this->extension = $this->createMock(FormConfigExtension::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(NormalizerInterface::class, new CustomFieldNormalizer($this->transformer, $this->extension));
    }

    public function testNormalize()
    {
        $config = $this->getMockBuilder(CustomField::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('name');

        $this->transformer->expects($this->once())
            ->method('transform')
            ->with($this->identicalTo($config))
            ->willReturn(['key1' => 'value1', 'key2' => 'value2']);

        $this->extension->expects($this->once())
            ->method('name')
            ->with($this->identicalTo($config))
            ->willReturn('name');

        $this->extension->expects($this->once())
            ->method('label')
            ->with($this->identicalTo($config))
            ->willReturn('label');

        $this->assertEquals([
            'name' => 'name',
            'type' => 'custom',
            'data' => [
                'type' => 'name',
                'label' => 'label',
                'form' => ['key1' => 'value1', 'key2' => 'value2'],
            ],
        ], (new CustomFieldNormalizer($this->transformer, $this->extension))->normalize($config));
    }

    public function testNormalizeUnsupportedObject()
    {
        $this->expectException(InvalidArgumentException::class);

        $normalizer = new CustomFieldNormalizer($this->transformer, $this->extension);
        $normalizer->normalize(new Object());
    }

    public function testSupportsNormalization()
    {
        $normalizer = new CustomFieldNormalizer($this->transformer, $this->extension);

        $this->assertFalse($normalizer->supportsNormalization(new Object()));
        $this->assertFalse($normalizer->supportsNormalization($this->createMock(FormConfigFieldInterface::class)));
        $this->assertTrue($normalizer->supportsNormalization($this->getMockBuilder(CustomField::class)->disableOriginalConstructor()->getMock()));
    }
}
