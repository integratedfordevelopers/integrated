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
     * Test publish time get- and setStartDate function
     */
    public function testGetAndSetPublishTimeStartDateFunction()
    {
        $publishedAt = new \DateTime();
        $this->assertSame($publishedAt, $this->content->getPublishTime()->setStartDate($publishedAt)->getStartDate());
    }

    /**
     * Test publish time get- and setEndDate function
     */
    public function testGetAndSetPublishTimeEndDateFunction()
    {
        $publishedUntil = new \DateTime();
        $this->assertSame($publishedUntil, $this->content->getPublishTime()->setEndDate($publishedUntil)->getEndDate());
    }

    /**
     * Test get- and setDisabled function
     */
    public function testGetAndSetDisabledFunction()
    {
        $this->assertTrue($this->content->setDisabled(true)->isDisabled());
        $this->assertFalse($this->content->setDisabled(false)->isDisabled());
    }

    /**
     * Test get- and setMetadata function
     */
    public function testGetAndSetMetadataFunction()
    {
        /* @var $metadata \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Metadata | \PHPUnit_Framework_MockObject_MockObject */
        $metadata = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Metadata');
        $this->assertSame($metadata, $this->content->setMetadata($metadata)->getMetadata());
    }

    /**
     * Test get- and setChannels function
     */
    public function testGetAndSetChannelsFunction()
    {
        $channels = new ArrayCollection();
        $this->assertSame($channels->toArray(), $this->content->setChannels($channels)->getChannels());
    }

    /**
     * Test addChannel function
     */
    public function testAddChannelFunction()
    {
        /* @var $channel \Integrated\Common\Content\Channel\ChannelInterface | \PHPUnit_Framework_MockObject_MockObject */
        $channel = $this->getMock('Integrated\Common\Content\Channel\ChannelInterface');

        // Check if we can add
        $this->content->addChannel($channel);
        $this->assertContains($channel, $this->content->getChannels());

        // Duplicate check
        $this->content->addChannel($channel);
        $this->assertCount(1, $this->content->getChannels());
    }

    /**
     * Test removeChannel function
     */
    public function testRemoveChannelFunction()
    {
        /* @var $channel1 \Integrated\Common\Content\Channel\ChannelInterface | \PHPUnit_Framework_MockObject_MockObject */
        $channel1 = $this->getMock('Integrated\Common\Content\Channel\ChannelInterface');

        /* @var $channel2 \Integrated\Common\Content\Channel\ChannelInterface | \PHPUnit_Framework_MockObject_MockObject */
        $channel2 = $this->getMock('Integrated\Common\Content\Channel\ChannelInterface');

        // Add channels
        $this->content->addChannel($channel1)->addChannel($channel2);
        $this->assertCount(2, $this->content->getChannels());

        // Remove channel2
        $this->assertSame($this->content, $this->content->removeChannel($channel2));

        // Channel1 should not be removed
        $this->assertContains($channel1, $this->content->getChannels());

        // Removal of channel that is not added
        $this->content->removeChannel($channel2);
        $this->assertCount(1, $this->content->getChannels());
    }
}
