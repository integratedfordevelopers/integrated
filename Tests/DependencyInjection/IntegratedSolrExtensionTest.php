<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\DependencyInjection;

use Integrated\Bundle\SolrBundle\DependencyInjection\IntegratedSolrExtension;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class IntegratedSolrExtensionTest extends \PHPUnit_Framework_TestCase
{
//    /**
//     * @var IntegratedSolrExtension
//     */
//    protected $extension;
//
//    /**
//     * Setup the test
//     */
//    protected function setUp()
//    {
//        $this->extension = new IntegratedSolrExtension();
//    }
//
//    /**
//     * Test getConfig function without directories
//     */
//    public function testGetConfigFunctionWithoutDirectories()
//    {
//        // Create config
//        $config = array();
//
//        /* @var $parameterBag \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface | \PHPUnit_Framework_MockObject_MockObject */
//        $parameterBag = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
//
//        /* @var $container \Symfony\Component\DependencyInjection\ContainerBuilder | \PHPUnit_Framework_MockObject_MockObject */
//        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
//
//        // Stub getParameterBag function
//        $container->expects($this->once())
//            ->method('getParameterBag')
//            ->will($this->returnValue($parameterBag));
//
//        /* @var $definitation \Symfony\Component\DependencyInjection\Definition | \PHPUnit_Framework_MockObject_MockObject */
//        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
//
//        // Stub getDefinition function
//        $container->expects($this->never())
//            ->method('getDefinition')
//            ->will($this->returnValue($definition));
//
//        // Load config
//        $this->extension->load($config, $container);
//    }
//
//    /**
//     * Test getConfig function with directories
//     */
//    public function testGetConfigFunctionWithDirectories()
//    {
//        // Create config
//        $config = array(
//            'integrated_solr' => array(
//                'mapping' => array(
//                    'directories' => array(
//                        'test_bundle' => array(
//                            'namespace_prefix' => 'TestBundle',
//                            'path' => 'Test\Bundle'
//                        )
//                    )
//                )
//            )
//        );
//
//        /* @var $parameterBag \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface | \PHPUnit_Framework_MockObject_MockObject */
//        $parameterBag = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
//
//        /* @var $container \Symfony\Component\DependencyInjection\ContainerBuilder | \PHPUnit_Framework_MockObject_MockObject */
//        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
//
//        // Stub getParameterBag function
//        $container->expects($this->once())
//            ->method('getParameterBag')
//            ->will($this->returnValue($parameterBag));
//
//        /* @var $definitation \Symfony\Component\DependencyInjection\Definition | \PHPUnit_Framework_MockObject_MockObject */
//        $definition = $this->getMock('Symfony\Component\DependencyInjection\Definition');
//
//        // Stub getDefinition function
//        $container->expects($this->once())
//            ->method('getDefinition')
//            ->will($this->returnValue($definition));
//
//        // Load config
//        $this->extension->load($config, $container);
//    }
}