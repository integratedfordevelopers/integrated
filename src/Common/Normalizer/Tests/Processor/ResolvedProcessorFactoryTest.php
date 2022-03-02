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

use Integrated\Common\Normalizer\ContainerFactory;
use Integrated\Common\Normalizer\ContainerFactoryInterface;
use Integrated\Common\Normalizer\Processor\ResolvedProcessor;
use Integrated\Common\Normalizer\Processor\ResolvedProcessorFactory;
use Integrated\Common\Normalizer\Processor\ResolvedProcessorFactoryInterface;
use ReflectionProperty;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedProcessorFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ContainerFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $factory;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(ContainerFactoryInterface::class);
    }

    public function testInterface()
    {
        self::assertInstanceOf(ResolvedProcessorFactoryInterface::class, $this->getInstance());
    }

    public function testConstructor()
    {
        $property = new ReflectionProperty(ResolvedProcessorFactory::class, 'factory');
        $property->setAccessible(true);

        self::assertSame($this->factory, $property->getValue($this->getInstance()));

        $this->factory = null;

        self::assertInstanceOf(ContainerFactory::class, $property->getValue($this->getInstance()));
    }

    public function testCreateContainer()
    {
        self::assertInstanceOf(ResolvedProcessor::class, $this->getInstance()->createProcessor([]));
    }

    /**
     * @return ResolvedProcessorFactory
     */
    protected function getInstance()
    {
        return new ResolvedProcessorFactory($this->factory);
    }
}
