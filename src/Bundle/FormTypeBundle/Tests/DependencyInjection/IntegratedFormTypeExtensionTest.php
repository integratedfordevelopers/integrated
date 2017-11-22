<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\FormTypeBundle\Tests\DependencyInjection;

use Integrated\Bundle\FormTypeBundle\DependencyInjection\IntegratedFormTypeExtension;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedFormTypeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntegratedFormTypeExtension
     */
    protected $extension;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->extension = new IntegratedFormTypeExtension();
    }

    /**
     * Test load function
     */
    public function testLoadFunction()
    {
        // Create config
        $config = array();

        /* @var $parameterBag \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface | \PHPUnit_Framework_MockObject_MockObject */
        $parameterBag = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');

        /* @var $container \Symfony\Component\DependencyInjection\ContainerBuilder | \PHPUnit_Framework_MockObject_MockObject */
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        // Stub getParameterBag function
        $container->expects($this->once())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        // Load config
        $this->extension->load($config, $container);
    }
}