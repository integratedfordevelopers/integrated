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
use Integrated\Common\Normalizer\Processor\ProcessorRegistryBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ProcessorRegistryBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddProcessor()
    {
        $processor1 = $this->getProcessor();
        $processor2 = $this->getProcessor();
        $processor3 = $this->getProcessor();

        $instance = $this->getInstance();

        $instance->addProcessor($processor1, 'class1');
        $instance->addProcessor($processor1, 'class2');
        $instance->addProcessor($processor1, 'class3');

        $instance->addProcessor($processor2, 'class1');
        $instance->addProcessor($processor2, 'class3');

        $instance->addProcessor($processor3, 'class3');

        $instance->addProcessor($processor1, 'class1');
        $instance->addProcessor($processor1, 'class2');
        $instance->addProcessor($processor1, 'class3');

        $registry = $instance->getRegistry();

        self::assertSame([$processor1, $processor2], $registry->getProcessors('class1'));
        self::assertSame([$processor1], $registry->getProcessors('class2'));
        self::assertSame([$processor1, $processor2, $processor3], $registry->getProcessors('class3'));
    }

    public function testGetRegistry()
    {
        self::assertInstanceOf(ProcessorRegistry::class, $this->getInstance()->getRegistry());
    }

    /**
     * @return ProcessorRegistryBuilder
     */
    protected function getInstance()
    {
        return new ProcessorRegistryBuilder();
    }

    /**
     * @return ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getProcessor()
    {
        return $this->createMock(ProcessorInterface::class);
    }
}
