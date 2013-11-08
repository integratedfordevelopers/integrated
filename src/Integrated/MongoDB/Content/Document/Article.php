<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\Content\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Content;

/**
 * Document type Article
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 * @Content\Document("Article")
 */
class Article extends AbstractContent
{
    /**
     * {@inheritdoc}
     * @Content\Field(label="Title")
     */
    protected $name;

    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Subtitle")
     */
    protected $subtitle;

    /**
     * @var array Embedded\Author
     * @ODM\EmbedMany(targetDocument="Integrated\MongoDB\Content\Document\Embedded\Author", strategy="set")
     */
    protected $authors = array();

    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Source")
     */
    protected $source;

    /**
     * @var string
     * @ODM\String
     */
    protected $locale;

    /**
     * @var \DateTime
     * @ODM\Date
     * @Content\Field(label="Published until", type="datetime")
     */
    protected $publishedUntil;

    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Intro", type="textarea")
     */
    protected $intro;

    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Content", type="textarea")
     */
    protected $content;

    /**
     * @var Embedded\Location
     * @ODM\EmbedOne(targetDocument="Integrated\MongoDB\Content\Document\Embedded\Location")
     */
    protected $location;

    /**
     * Get the subtitle of the document
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the subtitle of the document
     *
     * @param string $subtitle
     * @return $this
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * Get the authors of the document
     *
     * @return array
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the authors of the document
     *
     * @param array $authors
     * @return $this
     */
    public function setAuthors(array $authors)
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * Get the source of the document
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source of the document
     *
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get the locale of the document
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the locale of the document
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Get the publishedUntil of the document
     *
     * @return \DateTime
     */
    public function getPublishedUntil()
    {
        return $this->publishedUntil;
    }

    /**
     * Set the publishedUntil of the document
     *
     * @param \DateTime $publishedUntil
     * @return $this
     */
    public function setPublishedUntil(\DateTime $publishedUntil)
    {
        $this->publishedUntil = $publishedUntil;
        return $this;
    }

    /**
     * Get the intro of the document
     *
     * @return string
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * Set the intro of the document
     *
     * @param string $intro
     * @return $this
     */
    public function setIntro($intro)
    {
        $this->intro = $intro;
        return $this;
    }

    /**
     * Get the content of the document
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content of the document
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the location of the document
     *
     * @return Embedded\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set the location of the document
     *
     * @param Embedded\Location $location
     * @return $this
     */
    public function setLocation(Embedded\Location $location)
    {
        $this->location = $location;
        return $this;
    }
}