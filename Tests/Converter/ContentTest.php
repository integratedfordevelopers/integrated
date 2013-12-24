<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Converter;

use Integrated\Bundle\SolrBundle\Converter\Content;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Content
     */
    protected $content;

    /**
     * @var \Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataFactory;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->metadataFactory = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory', array(), array(), '', false);
        $this->content = new Content($this->metadataFactory);
    }

    /**
     * Content should implement DriverInterface
     */
    public function testInstanceofConverterInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Solr\Converter\ConverterInterface', $this->content);
    }

    /**
     * Test getConfig function with invalid object
     *
     * @expectedException InvalidArgumentException
     */
    public function testGetConfigFunctionWithInvalidObject()
    {
        $object = 1;
        $this->content->getDocument($object);
    }

    /**
     * Test getConfig function with unknown object
     */
    public function testGetConfigFunctionWithUnknownObject()
    {
        $this->assertNull($this->content->getDocument($this));
    }

    /**
     * Test getConfig function with valid object with no index
     */
    public function testGetConfigWithValidObjectWithNoIndex()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->getMock('Integrated\Common\Content\ContentInterface');

        /* @var $metadataFactory \Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory | \PHPUnit_Framework_MockObject_MockObject */
        $metadataFactory = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory', array('getIndex', 'getFields'), array(), '', false);

        // Stub getIndex function
        $metadataFactory->expects($this->once())
            ->method('getIndex')
            ->will($this->returnValue(false));

        /* @var $metadataField \Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataField | \PHPUnit_Framework_MockObject_MockObject */
        $metadataField = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataField');

        // Stub getFields function
        $metadataFactory->expects($this->never())
            ->method('getFields')
            ->will($this->returnValue(array($metadataField)));

        // Stub build function
        $this->metadataFactory->expects($this->once())
            ->method('build')
            ->will($this->returnValue($metadataFactory));

        //Assert
        $this->assertNull($this->content->getDocument($content));
    }

    /**
     * Test getConfig function with valid object with index
     */
    public function testGetConfigWithValidObjectWithIndex()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->getMock('Integrated\Common\Content\ContentInterface');

        // Stub getId function
        $content->expects($this->once())
            ->method('getId')
            ->will($this->returnValue('123'));

        // Stub getType function
        $content->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('value'));

        /* @var $metadataFactory \Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory | \PHPUnit_Framework_MockObject_MockObject */
        $metadataFactory = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataFactory', array('getIndex', 'getFields'), array(), '', false);

        // Stub getIndex function
        $metadataFactory->expects($this->once())
            ->method('getIndex')
            ->will($this->returnValue(true));

        /* @var $metadataField \Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataField | \PHPUnit_Framework_MockObject_MockObject */
        $metadataField = $this->getMock('Integrated\Bundle\SolrBundle\Mapping\Metadata\MetadataField');

        // Stub getName function
        $metadataField->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('type'));

        // Stub getIndex function
        $metadataField->expects($this->once())
            ->method('getIndex')
            ->will($this->returnValue(true));

        // Stub getSort function
        $metadataField->expects($this->once())
            ->method('getSort')
            ->will($this->returnValue(true));

        // Stub getFacet function
        $metadataField->expects($this->once())
            ->method('getFacet')
            ->will($this->returnValue(true));

        // Stub getDisplay function
        $metadataField->expects($this->once())
            ->method('getDisplay')
            ->will($this->returnValue(true));

        // Stub getFields function
        $metadataFactory->expects($this->once())
            ->method('getFields')
            ->will($this->returnValue(array($metadataField)));

        // Stub build function
        $this->metadataFactory->expects($this->once())
            ->method('build')
            ->will($this->returnValue($metadataFactory));

        // Assert
        $this->assertInstanceOf('Solarium\QueryType\Update\Query\Document\DocumentInterface', $this->content->getDocument($content));
    }
}