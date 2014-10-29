<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Relation;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

use Symfony\Component\Validator\Constraints as Assert;

use Integrated\Common\Content\Relation\RelationInterface;
use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * Relation document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document(collection="relation")
 * @MongoDBUnique(fields="type")
 */
class Relation implements RelationInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ODM\String
     * @ODM\Index
     */
    protected $name;

    /**
     * @var string
     * @Assert\NotBlank
     * @ODM\String
     * @ODM\UniqueIndex
     */
    protected $type;

    /**
     * @var ContentTypeInterface[]
     * @ODM\ReferenceMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType")
     * @Assert\NotBlank()
     */
    protected $sources;

    /**
     * @var ContentTypeInterface[]
     * @ODM\ReferenceMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType")
     * @Assert\NotBlank()
     */
    protected $targets;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $multiple;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $required;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * Constructor, used to initialize some properties
     */
    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->targets = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the Relation
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
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Set the name of the Relation
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
     * {@inheritdoc}
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param Collection $sources
     * @return $this
     */
    public function setSources(Collection $sources)
    {
        $this->sources = new ArrayCollection();

        foreach ($sources as $source) {
            $this->addSource($source);
        }

        return $this;
    }

    /**
     * @param ContentTypeInterface $contentType
     * @return $this
     */
    public function addSource(ContentTypeInterface $contentType)
    {
        if (!$this->sources->contains($contentType)) {
            $this->sources->add($contentType);
        }

        return $this;
    }

    /**
     * @param ContentTypeInterface $contentType
     * @return bool true if this collection contained the specified element, false otherwise.
     */
    public function removeSource(ContentTypeInterface $contentType)
    {
        return $this->sources->removeElement($contentType);
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @param Collection $targets
     * @return $this
     */
    public function setTargets(Collection $targets)
    {
        $this->targets = new ArrayCollection();

        foreach ($targets as $target) {
            $this->addTarget($target);
        }

        return $this;
    }

    /**
     * @param ContentTypeInterface $contentType
     * @return $this
     */
    public function addTarget(ContentTypeInterface $contentType)
    {
        if (!$this->targets->contains($contentType)) {
            $this->targets->add($contentType);
        }

        return $this;
    }

    /**
     * @param ContentTypeInterface $contentType
     * @return bool true if this collection contained the specified element, false otherwise.
     */
    public function removeTarget(ContentTypeInterface $contentType)
    {
        return $this->targets->removeElement($contentType);
    }

    /**
     * {@inheritdoc}
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param boolean $multiple
     * @return $this
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Get the createdAt of the channel
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the channel
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
