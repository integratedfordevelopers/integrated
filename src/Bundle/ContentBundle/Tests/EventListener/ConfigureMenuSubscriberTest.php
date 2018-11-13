<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\EventListener;

use Integrated\Bundle\ContentBundle\EventListener\ConfigureMenuSubscriber;
use Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Test for ConfigureMenuSubscriber.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ConfigureMenuSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigureMenuSubscriber
     */
    protected $subscriber;

    /**
     * @var AuthorizationCheckerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $authorizationChecker;

    /**
     * @var \Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    /**
     * Setup the test.
     */
    protected function setup()
    {
        $this->event = $this->getMockBuilder(ConfigureMenuEvent::class)->disableOriginalConstructor()->getMock();
        $this->authorizationChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->subscriber = new ConfigureMenuSubscriber($this->authorizationChecker);
    }

    /**
     * Test getSubscribedEvents.
     */
    public function testGetSubscribedEventsFunction()
    {
        $this->assertArrayHasKey('integrated_menu.configure', ConfigureMenuSubscriber::getSubscribedEvents());
    }

    /**
     * Test onMenuConfigure function with invalid menu.
     */
    public function testOnMenuConfigureFunctionWithInvalidMenu()
    {
        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->event
            ->expects($this->once())
            ->method('getMenu')
            ->willReturn($menu)
        ;

        $menu
            ->expects($this->once())
            ->method('getName')
            ->willReturn('invalid_menu_name')
        ;

        $menu
            ->expects($this->never())
            ->method('getChild')
        ;

        $this->subscriber->onMenuConfigure($this->event);
    }

    /**
     * Test onMenuConfigure function with valid menu and content menu.
     */
    public function testOnMenuConfigureFunctionWithValidMenuAndContentMenu()
    {
        $menu = $this->getValidMenu($this->event);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $subMenu */
        $subMenu = $this->createMock('Knp\Menu\ItemInterface');

        $menu
            ->expects($this->once())
            ->method('getChild')
            ->with(ConfigureMenuSubscriber::MENU_CONTENT)
            ->willReturn($subMenu)
        ;

        $subMenu
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        $this->subscriber->onMenuConfigure($this->event);
    }

    /**
     * Test onMenuConfigure function with valid menu and no content menu.
     */
    public function testOnMenuConfigureFunctionWithValidMenuAndNoContentMenu()
    {
        $menu = $this->getValidMenu($this->event);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menuContent */
        $menuContent = $this->createMock('Knp\Menu\ItemInterface');

        $menu
            ->expects($this->once())
            ->method('getChild')
            ->with(ConfigureMenuSubscriber::MENU_CONTENT)
            ->willReturn(null)
        ;

        $menu
            ->expects($this->once())
            ->method('addChild')
            ->with(ConfigureMenuSubscriber::MENU_CONTENT)
            ->willReturn($menuContent)
        ;

        $menuContent
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        $this->subscriber->onMenuConfigure($this->event);
    }

    /**
     * Test onMenuConfigure function with valid menu and with content menu and with manage menu.
     */
    public function testOnMenuConfigureFunctionWithValidMenuAndWithContentMenuAndWithManageMenu()
    {
        $menu = $this->getValidMenu($this->event);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menuContent */
        $menuContent = $this->createMock('Knp\Menu\ItemInterface');

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menuContent */
        $menuManage = $this->createMock('Knp\Menu\ItemInterface');

        $menu
            ->expects($this->exactly(2))
            ->method('getChild')
            ->withConsecutive(
                [ConfigureMenuSubscriber::MENU_CONTENT],
                [ConfigureMenuSubscriber::MENU_MANAGE]
            )
            ->willReturnOnConsecutiveCalls(
                $menuContent,
                $menuManage
            )
        ;

        $menu
            ->expects($this->never())
            ->method('addChild')
        ;

        $menuContent
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        $menuManage
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        $this->authorizationChecker
            ->expects($this->atLeast(2))
            ->method('isGranted')
            ->with(ConfigureMenuSubscriber::ROLE_ADMIN)
            ->willReturn(true)
        ;

        $this->subscriber->onMenuConfigure($this->event);
    }

    /**
     * Test onMenuConfigure function with valid menu and with no content menu and with no manage menu.
     */
    public function testOnMenuConfigureFunctionWithValidMenuAndWithNoContentMenuAndWithNoManageMenu()
    {
        $menu = $this->getValidMenu($this->event);

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menuContent */
        $menuContent = $this->createMock('Knp\Menu\ItemInterface');

        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menuContent */
        $menuManage = $this->createMock('Knp\Menu\ItemInterface');

        $menu
            ->expects($this->exactly(2))
            ->method('getChild')
            ->withConsecutive(
                [ConfigureMenuSubscriber::MENU_CONTENT],
                [ConfigureMenuSubscriber::MENU_MANAGE]
            )
            ->willReturn(null)
        ;

        $menu
            ->expects($this->exactly(2))
            ->method('addChild')
            ->withConsecutive(
                [ConfigureMenuSubscriber::MENU_CONTENT],
                [ConfigureMenuSubscriber::MENU_MANAGE]
            )
            ->willReturnOnConsecutiveCalls(
                $menuContent,
                $menuManage
            )
        ;

        $menuContent
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        $menuManage
            ->expects($this->atLeastOnce())
            ->method('addChild')
        ;

        // Stub isGranted
        $this->authorizationChecker
            ->expects($this->atLeast(2))
            ->method('isGranted')
            ->with(ConfigureMenuSubscriber::ROLE_ADMIN)
            ->willReturn(true)
        ;

        $this->subscriber->onMenuConfigure($this->event);
    }

    /**
     * @param \Integrated\Bundle\MenuBundle\Event\ConfigureMenuEvent | \PHPUnit_Framework_MockObject_MockObject $event
     *
     * @return \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getValidMenu($event = null)
    {
        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $menu
            ->expects($this->once())
            ->method('getName')
            ->willReturn(ConfigureMenuSubscriber::MENU)
        ;

        if (null !== $event) {
            $event
                ->expects($this->once())
                ->method('getMenu')
                ->willReturn($menu)
            ;
        }

        return $menu;
    }
}
