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
use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use PHPUnit\Framework\TestCase;

class BreadcrumbProviderTest extends TestCase
{
    const VALID_MENU = 'breadcrumb';
    const INVALID_MENU = 'invalid_menu';

    /**
     * @var FactoryInterface | \PHPUnit_Framework_MockObject_MockObject
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
    protected function setUp(): void
    {
        $this->menuFactory = $this->createMock(FactoryInterface::class);
        $this->breadcrumbResolver = $this->createMock(BreadcrumbResolver::class);
        $this->breadcrumbMenuProvider = new BreadcrumbMenuProvider($this->menuFactory, $this->breadcrumbResolver);

        $menuItem1 = new BreadcrumbItem('Item 1', '/my');
        $menuItem2 = new BreadcrumbItem('Item 2', '/my/page');
        $this->breadcrumbResolver->method('getBreadcrumb')->willReturn([$menuItem1, $menuItem2]);
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->breadcrumbMenuProvider);
    }

    /**
     * Test has function with invalid menu.
     */
    public function testHasFunctionWithInvalidMenu()
    {
        $this->assertFalse($this->breadcrumbMenuProvider->has(self::INVALID_MENU));
    }

    /**
     * Test has function with valid menu.
     */
    public function testHasFunctionWithValidMenu()
    {
        $this->assertTrue($this->breadcrumbMenuProvider->has(self::VALID_MENU));
    }

    /**
     * Test get function with invalid menu.
     */
    public function testGetFunctionWithInvalidMenu()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->breadcrumbMenuProvider->get(self::INVALID_MENU);
    }
}
