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
use Integrated\Common\Bulk\Action\HandlerFactoryRegistryBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class HandlerFactoryRegistryBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddFactory()
    {
        $factory1 = $this->getFactory();
        $factory2 = $this->getFactory();

        $builder = $this->getInstance();

        $builder->addFactory('class1', $factory1);
        $builder->addFactory('class2', $factory1);
        $builder->addFactory('class3', $factory2);

        $registry = $builder->getRegistry();

        self::assertSame($factory1, $registry->getFactory('class1'));
        self::assertSame($factory1, $registry->getFactory('class2'));
        self::assertSame($factory2, $registry->getFactory('class3'));
    }

    public function testGetRegistry()
    {
        self::assertInstanceOf(HandlerFactoryRegistry::class, $this->getInstance()->getRegistry());
    }

    /**
     * @return HandlerFactoryRegistryBuilder
     */
    protected function getInstance()
    {
        return new HandlerFactoryRegistryBuilder();
    }

    /**
     * @return HandlerFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFactory()
    {
        return $this->createMock(HandlerFactoryInterface::class);
    }
}
