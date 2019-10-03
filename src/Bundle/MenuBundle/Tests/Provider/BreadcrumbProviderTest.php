<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\MenuBundle\Tests\Provider;

use Integrated\Bundle\MenuBundle\Provider\BreadcrumbMenuProvider;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbItem;
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver;

/**
 * Test for BreadcrumbProvider.
 */
class BreadcrumbProviderTest extends \PHPUnit\Framework\TestCase
{
    const VALID_MENU = 'breadcrumb';
    const INVALID_MENU = 'invalid_menu';

    /**
     * @var \Knp\Menu\FactoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuFactory;

    /**
     * @var BreadcrumbMenuProvider | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbMenuProvider;

    /**
     * @var BreadcrumbResolver | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbResolver;

    /**
     * Setup the test.
     */
    protected function setup()
    {
        $this->menuFactory = $this->createMock('Knp\Menu\FactoryInterface');
        $this->breadcrumbResolver = $this->createMock('Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver');
        $this->breadcrumbMenuProvider = new BreadcrumbMenuProvider($this->menuFactory, $this->breadcrumbResolver);

        $menuItem1 = new BreadcrumbItem('Item 1', '/url1');
        $menuItem2 = new BreadcrumbItem('Item 2', '/url2');
        $this->breadcrumbResolver->method('getBreadCrumb')->willReturn([$menuItem1, $menuItem2]);
    }

    /**
     * Test instanceOf.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Knp\Menu\Provider\MenuProviderInterface', $this->breadcrumbMenuProvider);
    }

    /**
     * Test has function with invalid menu.
     */
    public function testHasFunctionWithInvalidMenu()
    {
        $this->expectException(\Exception::class);

        $this->breadcrumbMenuProvider->has(self::INVALID_MENU);
    }

    /**
     * Test has function with valid menu.
     */
    public function testHasFunctionWithValidMenu()
    {
        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject  $menu */
        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->menuFactory
            ->expects($this->once())
            ->method('createItem')
            ->with(self::VALID_MENU)
            ->willReturn($menu);

        $this->assertTrue($this->breadcrumbMenuProvider->has(self::VALID_MENU));
    }

    /**
     * Test get function with invalid menu.
     */
    public function testGetFunctionWithInvalidMenu()
    {
        $this->expectException(\Exception::class);

        $this->breadcrumbMenuProvider->get(self::INVALID_MENU);
    }
}
