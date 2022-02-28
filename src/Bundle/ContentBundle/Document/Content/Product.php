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
     * @var float
     * @Type\Field(type="Symfony\Component\Form\Extension\Core\Type\MoneyType")
     */
    protected $price;

    /**
     * @var int
     * @Type\Field(
     *     type="Symfony\Component\Form\Extension\Core\Type\IntegerType",
     *     options={
     *         "label"="Stock quantity"
     *     }
     * )
     */
    protected $stockQuantity;

    /**
     * @var bool
     * @Type\Field(
     *     type="Symfony\Component\Form\Extension\Core\Type\CheckboxType",
     *     options={
     *         "attr"={"align_with_widget"=true}
     *     }
     * )
     */
    protected $orderable;

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
    public function getTitle(): ?string
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
    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the slug of the document.
     *
     * @return string
     */
    public function getSlug(): ?string
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
    public function setSlug($slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the reference of the document.
     *
     * @return string
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * Set the reference of the document.
     *
     * @param string $reference
     *
     * @return $this
     */
    public function setReference(string $reference): self
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
     *
     * @return $this
     */
    public function setVariant(string $variant): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Get the locale of the document.
     *
     * @return string
     */
    public function getLocale(): ?string
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
    public function setLocale($locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get price of the product.
     *
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Set price of the product.
     *
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get stock quantity.
     *
     * @return int
     */
    public function getStockQuantity(): ?int
    {
        return $this->stockQuantity;
    }

    /**
     * Set stock quantity.
     *
     * @param int $stockQuantity
     *
     * @return $this
     */
    public function setStockQuantity(int $stockQuantity): self
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
    }

    /**
     * Get orderable status of the product.
     *
     * @return bool
     */
    public function isOrderable(): bool
    {
        return (bool) $this->orderable;
    }

    /**
     * Set orderable status of the product.
     *
     * @param bool $orderable
     *
     * @return $this
     */
    public function setOrderable(bool $orderable): self
    {
        $this->orderable = $orderable;

        return $this;
    }

    /**
     * Get description of the product.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set description of the product.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the content of the document.
     *
     * @return string
     */
    public function getContent(): ?string
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
    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the cover image for product.
     *
     * @return StorageInterface
     */
    public function getCover(): ?StorageInterface
    {
        $items = $this->getReferencesByRelationTypes(['cover', 'embedded']);
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
    public function __toString(): string
    {
        return (string) $this->title;
    }
}
