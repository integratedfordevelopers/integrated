<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Mapping\Driver;

use Integrated\Bundle\SolrBundle\Mapping\Driver\FileLocator;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FileLocatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileLocator
     */
    protected $fileLocator;

    /**
     * @var array
     */
    protected $directories = array(
        'test1',
        'test2'
    );

    /**
     * Setup the test
     */
    protected function setUp()
    {
        // Create Driver
        $this->fileLocator = new FileLocator($this->directories);
    }

    /**
     * Test getDirectories function
     */
    public function testGetDirectoriesFunction()
    {
        $this->assertSame($this->directories, $this->fileLocator->getDirectories());
    }

    /**
     * Test setDirectories function
     */
    public function testSetDirectoriesFunction()
    {
        $directories = array('test 1');
        $this->assertSame($this->fileLocator, $this->fileLocator->setDirectories($directories));
        $this->assertSame($directories, $this->fileLocator->getDirectories());
    }

    /**
     * Test getFinder functions
     */
    public function testGetFinderFunction()
    {
        $this->assertInstanceOf('\Symfony\Component\Finder\Finder', $this->fileLocator->getFinder());
    }

    /**
     * Test setFinder function
     */
    public function testSetFinderFunction()
    {
        /* @var $finder \Symfony\Component\Finder\Finder | \PHPUnit_Framework_MockObject_MockObject */
        $finder = $this->getMock('Symfony\Component\Finder\Finder', array(), array(), '', false);
        $this->assertSame($finder, $this->fileLocator->setFinder($finder)->getFinder());
    }

    /**
     * Test getFile function with directories
     */
    public function testGetFileFunctionWithDirectories()
    {
        /* @var $finder \Symfony\Component\Finder\Finder | \PHPUnit_Framework_MockObject_MockObject */
        $finder = $this->getMock('Symfony\Component\Finder\Finder', array(), array(), '', false);

        // Stub ignoreUnreadableDirs function
        $finder->expects($this->once())
            ->method('ignoreUnreadableDirs')
            ->will($this->returnValue($finder));

        // Stub in function
        $finder->expects($this->once())
            ->method('in')
            ->will($this->returnValue($finder));

        //Stub name function
        $finder->expects($this->once())
            ->method('name')
            ->will($this->returnValue($finder));

        // Set finder
        $this->fileLocator->setFinder($finder);

        // Asserts
        $this->assertSame($finder, $this->fileLocator->getFiles('xml'));
    }

    /**
     * Test getFile function without directories
     */
    public function testGetFileFunctionWithoutDirectories()
    {
        /* @var $finder \Symfony\Component\Finder\Finder | \PHPUnit_Framework_MockObject_MockObject */
        $finder = $this->getMock('Symfony\Component\Finder\Finder', array(), array(), '', false);

        // Set no directories
        $this->fileLocator->setDirectories(array());

        // Stub ignoreUnreadableDirs function
        $finder->expects($this->never())
            ->method('ignoreUnreadableDirs')
            ->will($this->returnValue($finder));

        // Stub in function
        $finder->expects($this->never())
            ->method('in')
            ->will($this->returnValue($finder));

        //Stub name function
        $finder->expects($this->never())
            ->method('name')
            ->will($this->returnValue($finder));

        // Set finder
        $this->fileLocator->setFinder($finder);

        // Asserts
        $this->assertSame($finder, $this->fileLocator->getFiles('xml'));
    }
}