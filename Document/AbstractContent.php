<?php
namespace Integrated\Bundle\ContentBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Abstract base class for content types
 *
 * @package Integrated\Bundle\ContentBundle\Document
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 * @ODM\InheritanceType("SINGLE_COLLECTION")
 * @ODM\DiscriminatorField(fieldName="class")
 * @ODM\DiscriminatorMap({
 *      "Article"="Integrated\Bundle\ContentBundle\Document\Article",
 *      "File"="Integrated\Bundle\ContentBundle\Document\File",
 *      "Relation\Company"="Integrated\Bundle\ContentBundle\Document\Relation\Company",
 *      "Relation\Person"="Integrated\Bundle\ContentBundle\Document\Relation\Person"
 * })
 */
abstract class AbstractContent
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     */
    protected $type;

    /**
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * @var ArrayCollection
     * @ODM\ReferenceMany(discriminatorField="class")
     */
    protected $references;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $publishedAt;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $disabled;

    /**
     * @var array
     * @ODM\Hash
     */
    protected $metadata = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->references = new ArrayCollection();
    }

    /**
     * Get the id of the document
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the document
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the type of the document
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the document
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get the name of the document
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of the document
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the references of the document
     *
     * @return ArrayCollection
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Set the references of the document
     *
     * @param ArrayCollection $references
     * @return $this
     */
    public function setReferences(ArrayCollection $references)
    {
        $this->references = $references;
        return $this;
    }

    /**
     * Get the createdAt of the document
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the document
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get the updatedAt of the document
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the updatedAt of the document
     *
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get the publishedAt of the document
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set the publishedAt of the document
     *
     * @param \DateTime $publishedAt
     * @return $this
     */
    public function setPublishedAt(\DateTime $publishedAt)
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    /**
     * Get the disabled of the document
     *
     * @return bool
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set the disabled of the document
     *
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Get the metadata of the document
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Set the metadata of the document
     *
     * @param array $metadata
     * @return $this
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }
}