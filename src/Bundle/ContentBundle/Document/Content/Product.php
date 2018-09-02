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

use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Content\Document\Storage\FileInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Document type Product.
 *
 * @Type\Document("Product")
 */
class Product extends Content
{
    /**
     * @var string
     * @Type\Field
     */
    protected $title;

    /**
     * @var string
     * @Slug(fields={"title"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var string
     * @Type\Field
     */
    protected $reference;

    /**
     * @var string
     * @Type\Field
     */
    protected $variant;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     * @Type\Field(type="Symfony\Component\Form\Extension\Core\Type\TextareaType")
     */
    protected $description;

    /**
     * @var string
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\EditorType")
     */
    protected $content;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the title of the document.
     *
     * @return string
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the document.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title) : Product
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the slug of the document.
     *
     * @return string
     */
    public function getSlug() : ?string
    {
        return $this->slug;
    }

    /**
     * Set the slug of the document.
     *
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug) : Product
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the reference of the document
     *
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * Set the reference of the document
     *
     * @param string $reference
     *
     * @return $this
     */
    public function setReference(string $reference): Product
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get the variant of the document.
     *
     * @return string
     */
    public function getVariant(): ?string
    {
        return $this->variant;
    }

    /**
     * Set the variant of the document.
     *
     * @param string $variant
     */
    public function setVariant(string $variant): Product
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Get the locale of the document.
     *
     * @return string
     */
    public function getLocale() : ?string
    {
        return $this->locale;
    }

    /**
     * Set the locale of the document.
     *
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale) : Product
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description) : Product
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the content of the document.
     *
     * @return string
     */
    public function getContent() : ?string
    {
        return $this->content;
    }

    /**
     * Set the content of the document.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content) : Product
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the relative cover image URL for article.
     *
     * @return StorageInterface
     */
    public function getCover() : ?StorageInterface
    {
        $items = $this->getReferencesByRelationType('embedded');
        if ($items) {
            foreach ($items as $item) {
                if ($item instanceof FileInterface) {
                    if ($item->getFile() instanceof StorageInterface) {
                        return $item->getFile();
                    }
                }
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function __toString() : ?string
    {
        return $this->title;
    }
}
