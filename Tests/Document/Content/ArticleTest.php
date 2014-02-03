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
use Integrated\Bundle\ContentBundle\Document\Content\Article;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ArticleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Article
     */
    private $article;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->article = new Article();
    }

    /**
     * Article should implement ContentInterface
     */
    public function testInstanceOfContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->article);
    }

    /**
     * Article should extend Content
     */
    public function testInstanceOfContent()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\Content', $this->article);
    }

    /**
     * Test get- and setId function
     */
    public function testGetAndSetIdFunction()
    {
        $id = 'abc123';
        $this->assertEquals($id, $this->article->setId($id)->getId());
    }

    /**
     * Test get- and setContentType function
     */
    public function testGetAndSetContentTypeFunction()
    {
        $contentType = 'type';
        $this->assertEquals($contentType, $this->article->setContentType($contentType)->getContentType());
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
        $this->assertSame($this->article, $this->article->setRelations($relations));
        $this->assertEquals($relations, $this->article->setRelations($relations)->getRelations());
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
        $this->assertSame($this->article, $this->article->addReference($content));
        $this->assertSame($content, $this->article->getRelation('contentType')->getReferences()->first());
    }

    /**
     * Test removeReference function
     */
    public function testRemoveRelationFunction()
    {
        // Get empty collection with relations
        $relations = $this->article->getRelations();

        /* @var $relation \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation | \PHPUnit_Framework_MockObject_MockObject */
        $relation = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation');

        // Asserts
        $this->assertSame($this->article, $this->article->addRelation($relation));
        $this->assertSame($this->article, $this->article->removeRelation($relation));
        $this->assertSame($relations, $this->article->getRelations());
    }

    /**
     * Test get- and setCreatedAt function
     */
    public function testGetAndSetCreatedAtFunction()
    {
        $createdAt = new \DateTime();
        $this->assertSame($createdAt, $this->article->setCreatedAt($createdAt)->getCreatedAt());
    }

    /**
     * Test get- and setUpdated function
     */
    public function testGetAndSetUpdatedAtFunction()
    {
        $updatedAt = new \DateTime();
        $this->assertSame($updatedAt, $this->article->setUpdatedAt($updatedAt)->getUpdatedAt());
    }

    /**
     * Test get- and setPublishedAt function
     */
    public function testGetAndSetPublishedAtFunction()
    {
        $publishedAt = new \DateTime();
        $this->assertSame($publishedAt, $this->article->setPublishedAt($publishedAt)->getPublishedAt());
    }

    /**
     * Test get- and setDisabled function
     */
    public function testGetAndSetDisabledFunction()
    {
        $this->assertTrue($this->article->setDisabled(true)->getDisabled());
        $this->assertFalse($this->article->setDisabled(false)->getDisabled());
    }

    /**
     * Test get- and setMetadata function
     */
    public function testGetAndSetMetadataFunction()
    {
        $metadata = new ArrayCollection(array('key' => 'value'));
        $this->assertSame($metadata, $this->article->setMetadata($metadata)->getMetadata());
    }

    /**
     * Test addMetadata function
     */
    public function testAddMetadataFunction()
    {
        $metadata = new ArrayCollection(array('key' => 'value'));

        $this->assertSame($this->article, $this->article->setMetadata($metadata));
        $this->assertSame($this->article, $this->article->addMetadata('key2', 'value2'));
        $this->assertCount(2, $this->article->getMetadata());
    }

    /**
     * Test removeMetadata function
     */
    public function testRemoveMetadataFunction()
    {
        $metadata = new ArrayCollection(array('key' => 'value'));

        $this->assertSame($this->article, $this->article->setMetadata($metadata));
        $this->assertSame('value', $this->article->removeMetadata('key'));
        $this->assertCount(0, $this->article->getMetadata());
    }

    /**
     * Test get- and setTitle function
     */
    public function testGetAndSetTitleFunction()
    {
        $title = 'title';
        $this->assertSame($title, $this->article->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setSubtitle function
     */
    public function testGetAndSetSubtitleFunction()
    {
        $subtitle = 'subtitle';
        $this->assertEquals($subtitle, $this->article->setSubtitle($subtitle)->getSubtitle());
    }

    /**
     * Test get- and setAuthors function
     */
    public function testGetAndSetAuthorsFunction()
    {
        $authors = array('key' => 'value');
        $this->assertSame($authors, $this->article->setAuthors($authors)->getAuthors());
    }

    /**
     * Test get- and setSource function
     */
    public function testGetAndSetSourceFunction()
    {
        $source = 'source';
        $this->assertEquals($source, $this->article->setSource($source)->getSource());
    }

    /**
     * Test get- and setLocale function
     */
    public function testGetAndSetLocaleFunction()
    {
        $locale = 'locale';
        $this->assertEquals($locale, $this->article->setLocale($locale)->getLocale());
    }

    /**
     * Test get- and setPublishedUntil function
     */
    public function testGetAndSetPublishedUntilFunction()
    {
        $publishedUntil = new \DateTime();
        $this->assertSame($publishedUntil, $this->article->setPublishedUntil($publishedUntil)->getPublishedUntil());
    }

    /**
     * Test get- and setIntro function
     */
    public function testGetAndSetIntroFunction()
    {
        $intro = 'intro';
        $this->assertEquals($intro, $this->article->setIntro($intro)->getIntro());
    }

    /**
     * Test get- and setContent function
     */
    public function testGetAndSetContentFunction()
    {
        $content = 'content';
        $this->assertEquals($content, $this->article->setContent($content)->getContent());
    }

    /**
     * Test get- and setLocation function
     */
    public function testGetAndSetLocationFunction()
    {
        /* @var $location \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location | \PHPUnit_Framework_MockObject_MockObject */
        $location = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location');
        $this->assertSame($location, $this->article->setLocation($location)->getLocation());
    }
}