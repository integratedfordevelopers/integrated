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
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ArticleTest extends ContentTest
{
    /**
     * @var Article
     */
    private $article;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->article = new Article();
    }

    /**
     * Test get- and setTitle function.
     */
    public function testGetAndSetTitleFunction()
    {
        $title = 'title';
        $this->assertSame($title, $this->article->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setSubtitle function.
     */
    public function testGetAndSetSubtitleFunction()
    {
        $subtitle = 'subtitle';
        $this->assertEquals($subtitle, $this->article->setSubtitle($subtitle)->getSubtitle());
    }

    /**
     * Test get- and setAuthors function.
     */
    public function testGetAndSetAuthorsFunction()
    {
        $authors = new ArrayCollection(['key' => 'value']);
        $this->assertSame($authors, $this->article->setAuthors($authors)->getAuthors());
    }

    /**
     * Test addAuthor function.
     */
    public function testAddAuthorFunction()
    {
        /* @var $author \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author | \PHPUnit_Framework_MockObject_MockObject */
        $author = $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author');

        // Asserts
        $this->assertSame($this->article, $this->article->addAuthor($author));
        $this->assertCount(1, $this->article->getAuthors());
    }

    /**
     * Test addAuthor function with duplicate author.
     */
    public function testAddAuthorFunctionWithSameAuthor()
    {
        /* @var $author \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author | \PHPUnit_Framework_MockObject_MockObject */
        $author = $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author');

        // Add author two times
        $this->article->addAuthor($author)->addAuthor($author);

        // Asserts
        $this->assertCount(1, $this->article->getAuthors());
    }

    /**
     * Test removeAuthor function.
     */
    public function testRemoveAuthorFunction()
    {
        /* @var $author \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author | \PHPUnit_Framework_MockObject_MockObject */
        $author = $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author');

        // Add author
        $this->article->addAuthor($author);

        // Assert
        $this->assertTrue($this->article->removeAuthor($author));
    }

    /**
     * Test removeAuthor function with unknown author.
     */
    public function testRemoveAuthorFunctionWithUnknownAuthor()
    {
        /* @var $author \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author | \PHPUnit_Framework_MockObject_MockObject */
        $author = $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author');

        // Assert
        $this->assertFalse($this->article->removeAuthor($author));
    }

    /**
     * Test get- and setSource function.
     */
    public function testGetAndSetSourceFunction()
    {
        $source = 'source';
        $this->assertEquals($source, $this->article->setSource($source)->getSource());
    }

    /**
     * Test get- and setSourceUrl function.
     */
    public function testGetAndSetSourceUrlFunction()
    {
        $sourceUrl = 'sourceUrl';
        $this->assertEquals($sourceUrl, $this->article->setSourceUrl($sourceUrl)->getSourceUrl());
    }

    /**
     * Test get- and setLocale function.
     */
    public function testGetAndSetLocaleFunction()
    {
        $locale = 'locale';
        $this->assertEquals($locale, $this->article->setLocale($locale)->getLocale());
    }

    /**
     * Test get- and setIntro function.
     */
    public function testGetAndSetIntroFunction()
    {
        $intro = 'intro';
        $this->assertEquals($intro, $this->article->setIntro($intro)->getIntro());
    }

    /**
     * Test get- and setContent function.
     */
    public function testGetAndSetContentFunction()
    {
        $content = 'content';
        $this->assertEquals($content, $this->article->setContent($content)->getContent());
    }

    /**
     * Test address get- and setLocation function.
     */
    public function testGetAndSetAddressLocationFunction()
    {
        $this->article->setAddress(new Address());

        $location = new Location();
        $this->assertSame($location, $this->article->getAddress()->setLocation($location)->getLocation());
    }

    /**
     * Test toString function.
     */
    public function testToStringFunction()
    {
        $title = 'Title';
        $this->assertEquals($title, (string) $this->article->setTitle($title));
    }

    /**
     * {@inheritdoc}
     */
    protected function getContent()
    {
        return $this->article;
    }
}
