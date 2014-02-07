<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Debug;
use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Content
     */
    private $content;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->content = new Content();
    }

    /**
     * Content should implement ContentInterface
     */
    public function testInstanceOfContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->content);
    }

    /**
     * Test get- and setId function
     */
    public function testGetAndSetIdFunction()
    {
        $id = 'abc123';
        $this->assertEquals($id, $this->content->setId($id)->getId());
    }

    /**
     * Test get- and setContentType function
     */
    public function testGetAndSetContentTypeFunction()
    {
        $contentType = 'type';
        $this->assertEquals($contentType, $this->content->setContentType($contentType)->getContentType());
    }

    /**
     * Test get- and setRelations function
     */
    public function testGetAndSetRelationsFunction()
    {
        /* @var $relation \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation | \PHPUnit_Framework_MockObject_MockObject */
        $relation = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation');

        // Stub getReferences
        $relation->expects($this->once())
            ->method('getReferences')
            ->will($this->returnValue(new ArrayCollection()));

        // Create relations collection
        $relations = new ArrayCollection(array($relation));

        // Asserts
        $this->assertSame($this->content, $this->content->setRelations($relations));
        $this->assertEquals($relations, $this->content->setRelations($relations)->getRelations());
    }

    /**
     * Test addReference function
     */
    public function testAddReferenceFunction()
    {
        /* @var $content \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content = $this->getMock('Integrated\Common\Content\ContentInterface');

        // Stub getContentType
        $content->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('contentType'));

        // Asserts
        $this->assertSame($this->content, $this->content->addReference($content));
        $this->assertSame($content, $this->content->getRelation('contentType')->getReferences()->first());
    }

    /**
     * Test addReference function with two contentTypes
     */
    public function testAddReferenceFunctionWithTwoContentTypes()
    {
        /* @var $content1 \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content1 = $this->getMock('Integrated\Common\Content\ContentInterface');

        // Stub getContentType
        $content1->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('contentType1'));

        $this->content->addReference($content1);

        /* @var $content2 \Integrated\Common\Content\ContentInterface | \PHPUnit_Framework_MockObject_MockObject */
        $content2 = $this->getMock('Integrated\Common\Content\ContentInterface');

        // Stub getContentType
        $content2->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('contentType2'));

        $this->content->addReference($content2);

        // Asserts
        $this->assertCount(1, $this->content->getRelations());
    }

    /**
     * Test removeReference function
     */
    public function testRemoveRelationFunction()
    {
        // Get empty collection with relations
        $relations = $this->content->getRelations();

        /* @var $relation \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation | \PHPUnit_Framework_MockObject_MockObject */
        $relation = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation');

        // Asserts
        $this->assertSame($this->content, $this->content->addRelation($relation));
        $this->assertSame($this->content, $this->content->removeRelation($relation));
        $this->assertSame($relations, $this->content->getRelations());
    }

    /**
     * Test get- and setCreatedAt function
     */
    public function testGetAndSetCreatedAtFunction()
    {
        $createdAt = new \DateTime();
        $this->assertSame($createdAt, $this->content->setCreatedAt($createdAt)->getCreatedAt());
    }

    /**
     * Test get- and setUpdated function
     */
    public function testGetAndSetUpdatedAtFunction()
    {
        $updatedAt = new \DateTime();
        $this->assertSame($updatedAt, $this->content->setUpdatedAt($updatedAt)->getUpdatedAt());
    }

    /**
     * Test get- and setPublishedAt function
     */
    public function testGetAndSetPublishedAtFunction()
    {
        $publishedAt = new \DateTime();
        $this->assertSame($publishedAt, $this->content->setPublishedAt($publishedAt)->getPublishedAt());
    }

    /**
     * Test get- and setDisabled function
     */
    public function testGetAndSetDisabledFunction()
    {
        $this->assertTrue($this->content->setDisabled(true)->getDisabled());
        $this->assertFalse($this->content->setDisabled(false)->getDisabled());
    }

    /**
     * Test get- and setMetadata function
     */
    public function testGetAndSetMetadataFunction()
    {
        $metadata = array('key' => 'value');
        $this->assertSame($metadata, $this->content->setMetadata($metadata)->getMetadata());
    }

    /**
     * Test addMetadata function
     */
    public function testAddMetadataFunction()
    {
        $metadata = array('key' => 'value');

        $this->assertSame($this->content, $this->content->setMetadata($metadata));
        $this->assertSame($this->content, $this->content->addMetadata('key2', 'value2'));
        $this->assertCount(2, $this->content->getMetadata());
    }

    /**
     * Test removeMetadata function
     */
    public function testRemoveMetadataFunction()
    {
        $metadata = array('key' => 'value');

        $this->assertSame($this->content, $this->content->setMetadata($metadata));
        $this->assertSame('value', $this->content->removeMetadata('key'));
        $this->assertCount(0, $this->content->getMetadata());
    }

    /**
     * Test removeMetadata function with invalid metadata key
     */
    public function testRemoveMetadataFunctionWithInvalidMetadataKey()
    {
        // Set metadata
        $metadata = array('key' => 'value');
        $this->content->setMetadata($metadata);

        // Asserts
        $this->assertFalse($this->content->removeMetadata('key2'));
    }
}