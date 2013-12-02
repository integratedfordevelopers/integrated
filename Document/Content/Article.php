<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Type;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable;

/**
 * Document type Article
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Article")
 */
class Article extends Content
{
    /**
     * @var Translatable
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable")
     * @Type\Field(type="translatable_text")
     */
    protected $title;

    /**
     * @var Translatable
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable")
     * @Type\Field(type="translatable_text")
     */
    protected $subtitle;

    /**
     * @var array Embedded\Author
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author", strategy="set")
     */
    protected $authors = array();

    /**
     * @var string
     * @ODM\String
     * @Type\Field
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
     * @Type\Field(type="datetime", options={"label" = "Published until"})
     */
    protected $publishedUntil;

    /**
     * @var Translatable
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable")
     * @Type\Field(type="translatable_textarea")
     */
    protected $intro;

    /**
     * @var Translatable
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Translatable")
     * @Type\Field(type="translatable_textarea")
     */
    protected $content;

    /**
     * @var Embedded\Location
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location")
     */
    protected $location;

    /**
     * Get the title of the document
     *
     * @return Translatable
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document
     *
     * @param Translatable $title
     * @return $this
     */
    public function setTitle(Translatable $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the subtitle of the document
     *
     * @return Translatable
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the subtitle of the document
     *
     * @param Translatable $subtitle
     * @return $this
     */
    public function setSubtitle(Translatable $subtitle)
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
     * @return Translatable
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * Set the intro of the document
     *
     * @param Translatable $intro
     * @return $this
     */
    public function setIntro(Translatable $intro)
    {
        $this->intro = $intro;
        return $this;
    }

    /**
     * Get the content of the document
     *
     * @return Translatable
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content of the document
     *
     * @param Translatable $content
     * @return $this
     */
    public function setContent(Translatable $content)
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