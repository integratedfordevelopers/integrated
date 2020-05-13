<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer\Tests;

use Integrated\Common\Normalizer\Normalizer;
use Integrated\Common\Normalizer\NormalizerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ResolvedProcessorInterface;
use Integrated\Common\Normalizer\Processor\ResolverInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class NormalizerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->resolver = $this->createMock(ResolverInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(NormalizerInterface::class, $this->getInstance());
    }

    public function testNormalize()
    {
        $object = new stdClass();
        $options = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $mock = $this->createMock(ResolvedProcessorInterface::class);
        $mock->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($object), $this->callback(function (Context $context) use ($options) {
                self::assertSame($options, $context->getOptions());

                return true;
            }))
            ->willReturn(['key' => 'value']);

        $this->resolver->expects($this->once())
            ->method('getProcessor')
            ->with($this->identicalTo($object))
            ->willReturn($mock);

        self::assertEquals(['key' => 'value'], $this->getInstance()->normalize($object, $options));
    }

    public function testNormalizeNoObject()
    {
        $this->resolver->expects($this->never())
            ->method($this->anything());

        self::assertEquals([], $this->getInstance()->normalize('this-is-not-a-object'));
    }

    /**
     * @return Normalizer
     */
    protected function getInstance()
    {
        return new Normalizer($this->resolver);
    }
}
