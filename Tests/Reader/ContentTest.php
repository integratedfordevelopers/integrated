<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Reader;

use Integrated\Bundle\SolrBundle\Reader\Content;

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
     * Test getConfig function
     */
    public function testGetConfigFunction()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->getMock('Integrated\Common\Content\ContentInterface');

        // Stub build function
        $return = '123';
        $this->metadataFactory->expects($this->once())
            ->method('build')
            ->will($this->returnValue($return));

        // Assert
        $this->assertSame($return, $this->content->getConfig($content));
    }
}