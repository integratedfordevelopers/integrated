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

use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Content\Document\Storage\FileInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;

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
     * @ODM\UniqueIndex(sparse=true)
     * @Slug(fields={"title"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $subtitle;

    /**
     * @var ArrayCollection Embedded\Author[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author")
     * @Type\Field(type="integrated_author", options={"label" = "Authors"})
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
     * @var string
     * @ODM\String
     * @Type\Field(type="textarea")
     */
    protected $intro;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="textarea")
     */
    protected $description;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="integrated_tinymce")
     */
    protected $content;

    /**
     * @var Embedded\Address
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address")
     * @Type\Field(type="integrated_address")
     */
    protected $address;

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
     * Get the slug of the document
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the slug of the document
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * Get the address of the document
     *
     * @return Embedded\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the address of the document
     *
     * @param Embedded\Address $address
     * @return $this
     */
    public function setAddress(Embedded\Address $address = null)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get the relative cover image URL for article
     *
     * @return string
     */
    public function getCover()
    {
        $items = $this->getReferencesByRelationType('embedded');
        if ($items) {
            foreach ($items as $item) {
                if ($item instanceof FileInterface) {
                    if ($item->getFile() instanceof StorageInterface) {
                        return $item->getFile()->getPathname();
                    }
                }
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
