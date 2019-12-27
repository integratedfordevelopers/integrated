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

use Integrated\Common\Normalizer\Processor\ProcessorInterface;
use Integrated\Common\Normalizer\Processor\ProcessorRegistry;
use Integrated\Common\Normalizer\Processor\RegistryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ProcessorRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ProcessorInterface[][] | \PHPUnit_Framework_MockObject_MockObject[][]
     */
    protected $processors = [];

    protected function setUp(): void
    {
        $this->processors['class1'][] = $this->createMock(ProcessorInterface::class);
        $this->processors['class1'][] = $this->createMock(ProcessorInterface::class);
        $this->processors['class2'][] = $this->createMock(ProcessorInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(RegistryInterface::class, $this->getInstance());
    }

    public function testHasProcessors()
    {
        $registry = $this->getInstance();

        self::assertTrue($registry->hasProcessors('class1'));
        self::assertTrue($registry->hasProcessors('class2'));
        self::assertFalse($registry->hasProcessors('class3'));
    }

    public function testHasProcessorsInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Normalizer\Exception\ExceptionInterface::class);

        $this->getInstance()->hasProcessors(42);
    }

    public function testGetProcessors()
    {
        $registry = $this->getInstance();

        self::assertSame($this->processors['class1'], $registry->getProcessors('class1'));
        self::assertSame($this->processors['class2'], $registry->getProcessors('class2'));
    }

    public function testGetProcessorsInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Normalizer\Exception\ExceptionInterface::class);

        $this->getInstance()->getProcessors(42);
    }

    public function testGetProcessorsNotFound()
    {
        $this->expectException(\Integrated\Common\Normalizer\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('there-are-no-processor-for-this-class');

        $this->getInstance()->getProcessors('there-are-no-processor-for-this-class');
    }

    /**
     * @return ProcessorRegistry
     */
    protected function getInstance()
    {
        return new ProcessorRegistry($this->processors);
    }
}
