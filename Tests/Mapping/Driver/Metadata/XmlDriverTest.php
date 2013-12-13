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

use Integrated\Bundle\SolrBundle\Mapping\Driver\Metadata\XmlDriver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class XmlDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Integrated\Bundle\SolrBundle\Mapping\Driver\FileLocator | \PHPUnit_Framework_MockObject_MockObject
     */
    private $fileLocator;

    /**
     * @var XmlDriver
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
        $this->driver = new XmlDriver($this->fileLocator);
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

        $xml = $this->getValidXml();

        // Stub getContents function
        $file->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($xml));

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

        $xml = $this->getValidXml();

        // Stub getContents function
        $file->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($xml));

        // Stub getFiles function
        $this->fileLocator->expects($this->once())
            ->method('getFiles')
            ->will($this->returnValue(array($file)));

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));
    }

    /**
     * Test loadMetadataForClass function with invalid config
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testLoadMetadataForClassFunctionWithInvalidConfig()
    {
        /* @var $class \ReflectionClass | \PHPUnit_Framework_MockObject_MockObject */
        $class = $this->getMock('ReflectionClass', array(), array(), '', false);

        // Stub getName function
        $class->expects($this->never())
            ->method('getName')
            ->will($this->returnValue('HenkDeVries'));

        /* @var $file \SplFileInfo | \PHPUnit_Framework_MockObject_MockObject */
        $file = $this->getMock('SplFileInfo', array('getContents'), array(), '', false);

        // Stub getContents function
        $file->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue($this->getInvalidXml()));

        // Stub getFiles function
        $this->fileLocator->expects($this->once())
            ->method('getFiles')
            ->will($this->returnValue(array($file)));

        // Assert
        $this->assertNull($this->driver->loadMetadataForClass($class));

        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @return string
     */
    protected function getValidXml()
    {
        return '
            <mapping>
                <documents>
                    <document class="Test" index="1">
                        <fields>
                            <field name="title" index="1" facet="0" display="1" sort="0"  />
                            <field name="subtitle" index="1" facet="0" sort="0"  />
                        </fields>
                    </document>
                    <document class="Test2" index="0">
                        <fields>
                            <field name="title" index="1" facet="0" sort="0"  />
                        </fields>
                    </document>
                    <document class="Test3" index="1" />
                </documents>
            </mapping>
        ';
    }

    protected function getInvalidXml()
    {
        return '
            <mapping>
                <document class="Test" index="1">
                    <fields>
                        <field name="title" index="1" facet="0" display="1" sort="0"  />
                        <field name="subtitle" index="1" facet="0" sort="0"  />
                    </fields>
                </document>
            </mapping>
        ';
    }
}