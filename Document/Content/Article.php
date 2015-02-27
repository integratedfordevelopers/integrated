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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Type;
use Integrated\Bundle\ContentBundle\Document\Content\Image as Image;

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
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $subtitle;

    /**
     * @var ArrayCollection Embedded\Author[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author")
     */
    protected $authors;

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
     * @Type\Field(type="integrated_datetime", options={"label" = "Published until"})
     */
    protected $publishedUntil;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="textarea")
     */
    protected $intro;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="integrated_tinymce")
     */
    protected $content;

    /**
     * @var Embedded\Location
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location")
     */
    protected $location;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->authors = new ArrayCollection();
    }

    /**
     * Get the title of the document
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

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
     * @return Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set the authors of the document
     *
     * @param Collection $authors
     * @return $this
     */
    public function setAuthors(Collection $authors)
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * Add author to authors collection
     *
     * @param Embedded\Author $author
     * @return $this
     */
    public function addAuthor(Embedded\Author $author)
    {
        if (!$this->authors->contains($author)) {
            $this->authors->add($author);
        }

        return $this;
    }

    /**
     * @param Embedded\Author $author
     * @return boolean true if this collection contained the specified element, false otherwise.
     */
    public function removeAuthor(Embedded\Author $author)
    {
        return $this->authors->removeElement($author);
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
    public function setPublishedUntil(\DateTime $publishedUntil = null)
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
    public function setLocation(Embedded\Location $location = null)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get the relative cover image URL for article
     *
     * @return string
     */
    public function getCoverUrl()
    {
        foreach ($this->getReferencesByRelationType('embedded') as $item) {
            if ($item instanceOf Image) {
                return $item->getWebPath();
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}