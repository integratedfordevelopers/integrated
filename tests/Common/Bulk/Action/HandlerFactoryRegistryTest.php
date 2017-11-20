<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Bulk\Action;

use Integrated\Common\Bulk\Action\HandlerFactoryInterface;
use Integrated\Common\Bulk\Action\HandlerFactoryRegistry;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class HandlerFactoryRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HandlerFactoryInterface[] | \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $factories = [];

    protected function setUp()
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

    /**
     * @expectedException \Integrated\Common\Bulk\Exception\UnexpectedTypeException
     */
    public function testHasFactoryInvalidArgument()
    {
        $this->getInstance()->hasFactory(42);
    }

    public function testGetFactory()
    {
        $registry = $this->getInstance();

        self::assertSame($this->factories['class1'], $registry->getFactory('class1'));
        self::assertSame($this->factories['class2'], $registry->getFactory('class2'));
    }

    /**
     * @expectedException \Integrated\Common\Bulk\Exception\UnexpectedTypeException
     */
    public function testGetFactoryInvalidArgument()
    {
        $this->getInstance()->getFactory(42);
    }

    /**
     * @expectedException \Integrated\Common\Bulk\Exception\InvalidArgumentException
     * @expectedExceptionMessage there-are-no-factories-for-this-class
     */
    public function testGetFactoryNotFound()
    {
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
