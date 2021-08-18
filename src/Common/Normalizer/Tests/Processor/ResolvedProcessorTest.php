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

use Exception;
use Integrated\Common\Normalizer\ContainerFactoryInterface;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;
use Integrated\Common\Normalizer\Processor\ResolvedProcessor;
use Integrated\Common\Normalizer\Processor\ResolvedProcessorInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContainerFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    /**
     * @var ProcessorInterface[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $processors = [];

    protected function setUp(): void
    {
        $this->factory = $this->createMock(ContainerFactoryInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(ResolvedProcessorInterface::class, $this->getInstance());
    }

    public function testProcess()
    {
        $container = $this->getContainer();
        $object = new stdClass();
        $context = $this->getContext();

        $this->factory->expects($this->once())
            ->method('createContainer')
            ->willReturn($container);

        $this->processors[] = $this->createMock(ProcessorInterface::class);
        $this->processors[] = $this->createMock(ProcessorInterface::class);
        $this->processors[] = $this->createMock(ProcessorInterface::class);

        foreach ($this->processors as $processor) {
            $processor->expects($this->once())
                ->method('process')
                ->with($this->identicalTo($container), $this->identicalTo($object), $this->identicalTo($context));
        }

        $container->expects($this->once())
            ->method('toArray')
            ->willReturn(['key1' => 'value1', 'key2' => 'value2']);

        self::assertEquals(['key1' => 'value1', 'key2' => 'value2'], $this->getInstance()->process($object, $context));
    }

    public function testProcessNoProcessors()
    {
        $this->factory->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->process(new stdClass(), $this->getContext());
    }

    public function testProcessOrder()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The test succeeded');

        $this->factory->expects($this->once())
            ->method('createContainer')
            ->willReturn($this->getContainer());

        $this->processors[] = $this->createMock(ProcessorInterface::class);
        $this->processors[] = $this->createMock(ProcessorInterface::class);

        $this->processors[0]->expects($this->any())
            ->method('process')
            ->willThrowException(new Exception('The test succeeded'));

        $this->processors[1]->expects($this->any())
            ->method('process')
            ->willThrowException(new Exception('The test failed'));

        $this->getInstance()->process(new stdClass(), $this->getContext());
    }

    /**
     * @return ResolvedProcessor
     */
    protected function getInstance()
    {
        return new ResolvedProcessor($this->processors, $this->factory);
    }

    /**
     * @return ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContainer()
    {
        return $this->createMock(ContainerInterface::class);
    }

    /**
     * @return Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getContext()
    {
        return $this->getMockBuilder(Context::class)->disableOriginalConstructor()->getMock();
    }
}
