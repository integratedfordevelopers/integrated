<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Tests\Action;

use Integrated\Common\Bulk\Action\HandlerFactoryInterface;
use Integrated\Common\Bulk\Action\HandlerFactoryRegistry;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class HandlerFactoryRegistryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HandlerFactoryInterface[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $factories = [];

    protected function setUp(): void
    {
        $this->factories['class1'] = $this->createMock(HandlerFactoryInterface::class);
        $this->factories['class2'] = $this->createMock(HandlerFactoryInterface::class);
    }

    public function testHasFactory()
    {
        $registry = $this->getInstance();

        self::assertTrue($registry->hasFactory('class1'));
        self::assertTrue($registry->hasFactory('class2'));
        self::assertFalse($registry->hasFactory('class3'));
    }

    public function testHasFactoryInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\UnexpectedTypeException::class);

        $this->getInstance()->hasFactory(42);
    }

    public function testGetFactory()
    {
        $registry = $this->getInstance();

        self::assertSame($this->factories['class1'], $registry->getFactory('class1'));
        self::assertSame($this->factories['class2'], $registry->getFactory('class2'));
    }

    public function testGetFactoryInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\UnexpectedTypeException::class);

        $this->getInstance()->getFactory(42);
    }

    public function testGetFactoryNotFound()
    {
        $this->expectException(\Integrated\Common\Bulk\Exception\InvalidArgumentException::class);
        $this->expectExceptionMessage('there-are-no-factories-for-this-class');

        $this->getInstance()->getFactory('there-are-no-factories-for-this-class');
    }

    /**
     * @return HandlerFactoryRegistry
     */
    protected function getInstance()
    {
        return new HandlerFactoryRegistry($this->factories);
    }
}
