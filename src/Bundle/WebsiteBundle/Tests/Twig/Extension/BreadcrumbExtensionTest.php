<?php
/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WebsiteBundle\Tests\Twig\Extension;

use Integrated\Bundle\MenuBundle\Provider\BreadcrumbMenuProvider;
use Integrated\Bundle\WebsiteBundle\Twig\Extension\BreadcrumbExtension;
use Knp\Menu\Twig\Helper;
use Twig\TwigFunction;

/**
 * Test for BreadcrumbExtension.
 */
class BreadcrumbExtensionTest extends \PHPUnit\Framework\TestCase
{
    const TEMPLATE = 'default';

    /**
     * @var Helper | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuTwigHelper;

    /**
     * @var BreadcrumbMenuProvider | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbMenuProvider;

    /**
     * @var BreadcrumbExtension | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbExtension;

    /**
     * Setup the test.
     */
    protected function setup()
    {
        $this->breadcrumbMenuProvider = $this->createMock('Integrated\Bundle\MenuBundle\Provider\BreadcrumbMenuProvider');
        $this->menuTwigHelper = $this->createMock('Knp\Menu\Twig\Helper');
        $this->breadcrumbExtension = new BreadcrumbExtension($this->breadcrumbMenuProvider, $this->menuTwigHelper, self::TEMPLATE);
    }

    /**
     * Test instanceOf.
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Twig\Extension\AbstractExtension', $this->breadcrumbExtension);
    }

    /**
     * Test render breadcrumb.
     */
    public function testRenderBreadcrumb()
    {
        $this->createMock('Integrated\Bundle\MenuBundle\Provider\BreadcrumbMenuProvider');

        $menu = $this->createMock('Knp\Menu\ItemInterface');

        $this->breadcrumbMenuProvider
            ->expects($this->once())
            ->method('get')
            ->willReturn([$menu]);

        $this->assertEquals('', $this->breadcrumbExtension->renderBreadcrumb());
    }

    /**
     * Test render breadcrumb.
     */
    public function testName()
    {
        $this->assertEquals('integrated_breadcrumb_menu', $this->breadcrumbExtension->getName());
    }

    /**
     * Test render breadcrumb.
     */
    public function testGetFunctions()
    {
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $this->breadcrumbExtension->getFunctions());
    }
}
