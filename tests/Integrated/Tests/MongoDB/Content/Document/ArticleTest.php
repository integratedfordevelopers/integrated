<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\Content\Document;

use Integrated\MongoDB\Content\Document\Article;

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
    public function testContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->article);
    }

    /**
     * Article should extend AbstractContent
     */
    public function testAbstractContent()
    {
        $this->assertInstanceOf('Integrated\MongoDB\Content\Document\AbstractContent', $this->article);
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
     * Test get- and setType function
     */
    public function testGetAndSetTypeFunction()
    {
        $type = 'type';
        $this->assertEquals($type, $this->article->setType($type)->getType());
    }

    /**
     * Test get- and setName function
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertEquals($name, $this->article->setName($name)->getName());
    }

    /**
     * Test get- and setReferences function
     */
    public function testGetAndSetReferencesFunction()
    {
        /* @var $references \Doctrine\Common\Collections\ArrayCollection | \PHPUnit_Framework_MockObject_MockObject */
        $references = $this->getMock('Doctrine\Common\Collections\ArrayCollection');
        $this->assertSame($references, $this->article->setReferences($references)->getReferences());
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
    }

    /**
     * Test get- and setMetadata function
     */
    public function testGetAndSetMetadataFunction()
    {
        $metadata = array('key' => 'value');
        $this->assertSame($metadata, $this->article->setMetadata($metadata)->getMetadata());
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
        /* @var $location \Integrated\MongoDB\Content\Document\Embedded\Location | \PHPUnit_Framework_MockObject_MockObject */
        $location = $this->getMock('Integrated\MongoDB\Content\Document\Embedded\Location');
        $this->assertSame($location, $this->article->setLocation($location)->getLocation());
    }
}