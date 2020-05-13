<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer\Tests\Processor;

use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ResolvedProcessorInterface;
use Integrated\Common\Normalizer\Processor\ResolverInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContextTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var Context | \PHPUnit_Framework_MockObject_MockObject
     */
    private $nesting = null;

    protected function setUp(): void
    {
        $this->resolver = $this->createMock(ResolverInterface::class);
    }

    public function testGetOptions()
    {
        self::assertSame($this->options, $this->getInstance()->getOptions());

        $this->options = ['key1' => 'value1', 'key2' => 'value2'];

        self::assertSame($this->options, $this->getInstance()->getOptions());
    }

    public function testGetNesting()
    {
        self::assertSame($this->nesting, $this->getInstance()->getNesting());

        $this->nesting = $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();

        self::assertSame($this->nesting, $this->getInstance()->getNesting());
    }

    public function testNormalize()
    {
        $object = new stdClass();
        $options = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $instance = $this->getInstance();

        $mock = $this->createMock(ResolvedProcessorInterface::class);
        $mock->expects($this->once())
            ->method('process')
            ->with($this->identicalTo($object), $this->callback(function (Context $context) use ($options, $instance) {
                self::assertSame($options, $context->getOptions());
                self::assertSame($instance, $context->getNesting());

                return true;
            }))
            ->willReturn(['key' => 'value']);

        $this->resolver->expects($this->once())
            ->method('getProcessor')
            ->with($this->identicalTo($object))
            ->willReturn($mock);

        self::assertEquals(['key' => 'value'], $instance->normalize($object, $options));
    }

    public function testNormalizeNoObject()
    {
        $this->resolver->expects($this->never())
            ->method($this->anything());

        self::assertEquals([], $this->getInstance()->normalize('this-is-not-a-object'));
    }

    /**
     * @return Context
     */
    protected function getInstance()
    {
        return new Context($this->resolver, $this->options, $this->nesting);
    }
}
