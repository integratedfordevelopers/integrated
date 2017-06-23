<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Bulk\Registry;

use Integrated\Bundle\ContentBundle\Bulk\ActionHandler\ActionHandlerInterface;
use Integrated\Bundle\ContentBundle\Bulk\Registry\ActionHandlerRegistry;
use Integrated\Bundle\ContentBundle\Bulk\Registry\ActionHandlerRegistryBuilder;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionHandlerRegistryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionHandlerRegistryBuilder
     */
    private $builder;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->builder = new ActionHandlerRegistryBuilder();
    }

    public function testBuilder()
    {
        $handler1 = $this->getMockBuilder(ActionHandlerInterface::class)->getMock();
        $handler2 = $this->getMockBuilder(ActionHandlerInterface::class)->getMock();

        $this->assertSame($this->builder, $this->builder->addHandler($handler1));
        $this->assertSame($this->builder, $this->builder->addHandler($handler2));
        $this->assertInstanceOf(ActionHandlerRegistry::class, $this->builder->getRegistry());
    }
}
