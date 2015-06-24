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

use Integrated\Bundle\MenuBundle\Provider\Provider;

/**
 * Test for Provider
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
{
    const VALID_MENU = 'integrated_menu';
    const INVALID_MENU = 'invalid_menu';

    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var \Knp\Menu\FactoryInterface | \PHPUnit_Framework_MockObject_MockObject $event
     */
    protected $factory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;

    /**
     * Setup the test
     */
    protected function setup()
    {
        $this->factory = $this->getMock('Knp\Menu\FactoryInterface');
        $this->eventDispatcher = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->provider = new Provider($this->factory, $this->eventDispatcher);
    }

    /**
     * Test instanceOf
     */
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Knp\Menu\Provider\MenuProviderInterface', $this->provider);
    }

    /**
     * Test has function with invalid menu
     */
    public function testHasFunctionWithInvalidMenu()
    {
        $this->assertFalse($this->provider->has(self::INVALID_MENU));
    }

    /**
     * Test has function with valid menu
     */
    public function testHasFunctionWithValidMenu()
    {
        $this->assertTrue($this->provider->has(self::VALID_MENU));
    }

    /**
     * Test get function with invalid menu
     * @expectedException \InvalidArgumentException
     */
    public function testGetFunctionWithInvalidMenu()
    {
        $this->provider->get(self::INVALID_MENU);
    }

    /**
     * Test get function twice with valid menu
     */
    public function testGetFunctionTwiceWithValidMenu()
    {
        /** @var \Knp\Menu\ItemInterface | \PHPUnit_Framework_MockObject_MockObject  $menu */
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        $this->factory
            ->expects($this->once())
            ->method('createItem')
            ->with(self::VALID_MENU)
            ->willReturn($menu)
        ;

        $this->eventDispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
        ;

        $this->provider->get(self::VALID_MENU);
        $this->provider->get(self::VALID_MENU);
    }
}
