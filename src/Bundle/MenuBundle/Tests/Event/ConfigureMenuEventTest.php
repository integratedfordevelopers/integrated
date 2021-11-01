<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Tests\Event;

use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;

/**
 * Test for ConfigureMenuEvent.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ConfigureMenuEventTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigureMenuEvent
     */
    protected $event;

    /**
     * @var \Knp\Menu\FactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $factory;

    /**
     * @var \Knp\Menu\ItemInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $menu;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->factory = $this->createMock('Knp\Menu\FactoryInterface');
        $this->menu = $this->createMock('Knp\Menu\ItemInterface');
        $this->event = new ConfigureMenuEvent($this->factory, $this->menu);
    }

    /**
     * Test instanceOf.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Symfony\Component\EventDispatcher\Event', $this->event);
    }

    /**
     * Test getFactory function.
     */
    public function testGetFactoryFunction()
    {
        $this->assertSame($this->factory, $this->event->getFactory());
    }

    /**
     * Test getMenu function.
     */
    public function testGetMenuFunction()
    {
        $this->assertSame($this->menu, $this->event->getMenu());
    }
}
