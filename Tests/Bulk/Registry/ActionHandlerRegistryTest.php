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

use Integrated\Bundle\ContentBundle\Bulk\ActionHandlerInterface;
use Integrated\Bundle\ContentBundle\Bulk\Registry\ActionHandlerRegistry;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ActionHandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionHandlerRegistry
     */
    private $registry;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->registry = new ActionHandlerRegistry();
    }

    public function testSetAndGetHandlers()
    {
        $handler1 = $this->getMockBuilder(ActionHandlerInterface::class)->getMock();
        $handler2 = $this->getMockBuilder(ActionHandlerInterface::class)->getMock();

        $this->assertSame($this->registry, $this->registry->setHandlers([$handler1, $handler2]));
        $this->assertCount(1, $this->registry->getHandlers());
        $this->assertArrayHasKey(get_class($handler2), $this->registry->getHandlers());
    }

    public function testAddAndHasHandler()
    {
        $handler = $this->getMockBuilder(ActionHandlerInterface::class)->getMock();

        $this->assertSame($this->registry, $this->registry->addHandler($handler));
        $this->assertTrue($this->registry->hasHandler(get_class($handler)));
        $this->assertFalse($this->registry->hasHandler('UNKNOWN_KEY'));
    }

    public function testGetHandler()
    {
        $handler = $this->getMockBuilder(ActionHandlerInterface::class)->getMock();

        $this->registry->addHandler($handler);

        $this->assertInstanceOf(ActionHandlerInterface::class, $this->registry->getHandler(get_class($handler)));
        $this->assertNull($this->registry->getHandler('UNKNOWN_KEY'));
    }
}
