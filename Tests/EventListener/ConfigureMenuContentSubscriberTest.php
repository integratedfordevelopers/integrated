<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\EventListener\Menu;

use Integrated\Bundle\ContentBundle\EventListener\ConfigureMenuSubscriber;

/**
 * Test for ConfigureMenuSubscriber
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ConfigureMenuSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigureMenuSubscriber
     */
    protected $subscriber;

    /**
     * Setup the test
     */
    protected function setup()
    {
        $this->subscriber = new ConfigureMenuSubscriber();
    }

    /**
     * Test getSubscribedEvents
     */
    public function testGetSubscribedEventsFunction()
    {
        $this->assertArrayHasKey('integrated_menu.configure', ConfigureMenuSubscriber::getSubscribedEvents());
    }

    /**
     * Test onMenuConfigure function with invalid menu
     */
    public function testOnMenuConfigureFunctionWithInvalidMenu()
    {
        /** @var \Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent | \PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent', [], [], '', false);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        // Stub event getMenu
        $event
            ->expects($this->once())
            ->method('getMenu')
            ->willReturn($menu)
        ;

        // Stub menu getName
        $menu
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn('invalid_menu_name')
        ;

        // Stub menu getChild, this function will not be called
        $menu
            ->expects($this->never())
            ->method('getChild')
        ;

        // Fire the event
        $this->subscriber->onMenuConfigure($event);
    }

    /**
     * Test onMenuConfigure function with content menu
     */
    public function testOnMenuConfigureFunctionWithContentMenu()
    {
        /** @var \Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent | \PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent', [], [], '', false);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        // Stub event getMenu
        $event
            ->expects($this->once())
            ->method('getMenu')
            ->willReturn($menu)
        ;

        // Stub menu getName
        $menu
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn(ConfigureMenuSubscriber::MENU_CONTENT)
        ;

        // Stub sub menu addChild
        $menu
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        // Fire the event
        $this->subscriber->onMenuConfigure($event);
    }

    /**
     * Test onMenuConfigure function with manage menu
     */
    public function testOnMenuConfigureFunctionWithManageMenu()
    {
        /** @var \Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent | \PHPUnit_Framework_MockObject_MockObject $event */
        $event = $this->getMock('Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent', [], [], '', false);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        // Stub event getMenu
        $event
            ->expects($this->once())
            ->method('getMenu')
            ->willReturn($menu)
        ;

        // Stub menu getName
        $menu
            ->expects($this->exactly(2))
            ->method('getName')
            ->willReturn(ConfigureMenuSubscriber::MENU_MANAGE)
        ;

        // Stub sub menu addChild
        $menu
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        // Fire the event
        $this->subscriber->onMenuConfigure($event);
    }
}
