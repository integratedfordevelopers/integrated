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
use Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver;
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
     * @var Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $menuTwigHelper;

    /**
     * @var BreadcrumbMenuProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbMenuProvider;

    /**
     * @var BreadcrumbResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbResolver;

    /**
     * @var BreadcrumbExtension|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $breadcrumbExtension;

    protected function setUp(): void
    {
        $this->breadcrumbMenuProvider = $this->createMock('Integrated\Bundle\MenuBundle\Provider\BreadcrumbMenuProvider');
        $this->menuTwigHelper = $this->createMock('Knp\Menu\Twig\Helper');
        $this->breadcrumbResolver = $this->createMock('Integrated\Bundle\PageBundle\Breadcrumb\BreadcrumbResolver');

        $this->breadcrumbExtension = new BreadcrumbExtension($this->breadcrumbMenuProvider, $this->menuTwigHelper, $this->breadcrumbResolver, self::TEMPLATE);
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('Twig\Extension\AbstractExtension', $this->breadcrumbExtension);
    }

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

    public function testName()
    {
        $this->assertEquals('integrated_breadcrumb_menu', $this->breadcrumbExtension->getName());
    }

    public function testGetFunctions()
    {
        $this->assertContainsOnlyInstancesOf(TwigFunction::class, $this->breadcrumbExtension->getFunctions());
    }
}
