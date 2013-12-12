<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Mapping\Driver\Metadata;

use Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\YamlDriver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class YamlDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Integrated\Bundle\SolrBundle\Mapping\Driver\FileLocator | \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileLocator;

    /**
     * @var YamlDriver
     */
    private $driver;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        // Mock FileLocator
        $this->fileLocator = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Driver\FileLocator');

        // Create Driver
        $this->driver = new YamlDriver($this->fileLocator);
    }

    /**
     * Driver should implement DriverInterface
     */
    public function testInstanceofDriverInterface()
    {
        $this->assertInstanceOf('Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\DriverInterface', $this->driver);
    }

    /**
     * Test loadMetadataForClass function with existing config
     */
    public function testLoadMetadataForClassFunctionWithExistingConfig()
    {
        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        // Stub getName function
        $class->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('Test'));

        /* @var $file \SplFileInfo | \PHPUnit_Framework_MockObject_MockObject */
        $file = $this->getMock('SplFileInfo', array('getContents'), array(), '', false);

        // Stub getContents function
        $file->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($this->getYaml()));

        // Stub getFiles function
        $this->fileLocator->expects($this->once())
            ->method('getFiles')
            ->will($this->returnValue(array($file)));

        // Assert
        $this->assertInstanceOf('Integrated\Bundle\SolrBundle\Mapping\Metadata\Metadata', $this->driver->loadMetadataForClass($class));
    }

    /**
     * Test loadMetadataForClass function with not existing config
     */
    public function testLoadMetadataForClassFunctionWithNotExistingConfig()
    {
        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        // Stub getName function
        $class->expects($this->exactly(1))
            ->method('getName')
            ->will($this->returnValue('HenkDeVries'));

        /* @var $file \SplFileInfo | \PHPUnit_Framework_MockObject_MockObject */
        $file = $this->getMock('SplFileInfo', array('getContents'), array(), '', false);

        // Stub getContents function
        $file->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($this->getYaml()));

        // Stub getFiles function
        $this->fileLocator->expects($this->once())
            ->method('getFiles')
            ->will($this->returnValue(array($file)));

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));
    }

    /**
     * @return string
     */
    protected function getYaml()
    {
        return "
'Test':
  index: true
  fields:
    title:
      facet: true
      index: treu
      sort: true
      display: true
'Test2':
  index: false
  field:
    title:
      index: true
      facet: true
      display: true

        ";
    }
}