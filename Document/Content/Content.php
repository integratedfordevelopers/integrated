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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Metadata;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime;

use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\ExtensibleInterface;
use Integrated\Common\Content\ExtensibleTrait;
use Integrated\Common\Content\MetadataInterface;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\RegistryInterface;

use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Abstract base class for document types
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document(collection="content", repositoryClass="Integrated\Bundle\ContentBundle\Document\Content\ContentRepository")
 * @ODM\Indexes({
 *   @ODM\Index(keys={"class"="asc"}),
 *   @ODM\Index(keys={"relations.references.$id"="asc", "class"="asc"})
 * })
 * @ODM\InheritanceType("SINGLE_COLLECTION")
 * @ODM\DiscriminatorField(fieldName="class")
 * @ODM\HasLifecycleCallbacks
 */
class Content implements ContentInterface, ExtensibleInterface, MetadataInterface, ChannelableInterface
{
    use ExtensibleTrait;

    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string the type of the ContentType
     * @ODM\String
     * @ODM\Index
     */
    protected $contentType;

    /**
     * @var ArrayCollection
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation")
     */
    protected $relations;

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
     * @var PublishTime
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime")
     * @Type\Field(type="integrated_publish_time")
     */
    protected $publishTime;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $published = true;

    /**
     * @var bool
     * @ODM\Boolean
     * @Type\Field(type="checkbox")
     */
    protected $disabled = false;

    /**
     * @var Metadata
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Metadata")
     */
    protected $metadata;

    /**
     * @var Collection
     * @ODM\ReferenceMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Channel\Channel")
     */
    protected $channels;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->relations = new ArrayCollection();
        $this->updatedAt = new \DateTime();
        $this->publishTime = new PublishTime();
        $this->channels = new ArrayCollection();
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
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * {@inheritdoc}
     */
    public function setRelations(Collection $relations)
    {
        foreach ($relations as $relation) {
            if ($relation instanceof Relation) {
                $this->addRelation($relation);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRelation(Relation $relation)
    {
        if ($exist = $this->getRelation($relation->getRelationId())) {
            $exist->addReferences($relation->getReferences());
        } else {
            $this->relations->add($relation);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRelation(Relation $relation)
    {
        $this->relations->removeElement($relation);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation($relationId)
    {
        return $this->relations->filter(function ($relation) use ($relationId) {
            if ($relation instanceof Relation) {
                if ($relation->getRelationId() == $relationId) {
                    return true;
                }
            }

            return false;
        })->first();
    }

    /**
     * @param $relationType
     * @return ArrayCollection|false
     */
    public function getRelationsByRelationType($relationType)
    {
        return $this->relations->filter(function ($relation) use ($relationType) {
            if ($relation instanceof Relation) {
                if ($relation->getRelationType() == $relationType) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param $relationType
     * @return array|bool
     */
    public function getReferencesByRelationType($relationType)
    {
        if ($relations = $this->getRelationsByRelationType($relationType)) {
            $references = array();

            /** @var Relation $relation */
            foreach ($relations as $relation) {
                $references = array_merge($references, $relation->getReferences()->toArray());
            }

            return $references;
        }

        return false;
    }

    /**
     * @param string $relationId
     * @param bool $published
     * @return ArrayCollection
     */
    public function getReferencesByRelationId($relationId, $published = true)
    {
        foreach ($this->relations as $relation) {
            if ($relation instanceof Relation) {
                if ($relation->getRelationId() == $relationId) {
                    if ($references = $relation->getReferences()) {
                        if (true !== $published) {
                            return $references;
                        }

                        return $references->filter(function ($content) {
                            return $content instanceof Content ? $content->isPublished() : true;
                        });
                    }
                }
            }
        }

        return new ArrayCollection();
    }

    /**
     * @param string $relationId
     * @param bool $published
     * @return Content|null
     */
    public function getReferenceByRelationId($relationId, $published = true)
    {
        if ($references = $this->getReferencesByRelationId($relationId, $published)) {
            return $references->first();
        }
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
     * Get the publish time of the document
     *
     * @return PublishTime
     */
    public function getPublishTime()
    {
        return $this->publishTime;
    }

    /**
     * Set the publish time of the document
     *
     * @param PublishTime $publishTime
     * @return $this
     */
    public function setPublishTime(PublishTime $publishTime)
    {
        $this->publishTime = $publishTime;
        return $this;
    }

    /**
     * Get the published of the document
     *
     * @deprecated
     * @return bool
     */
    public function getPublished()
    {
        return $this->isPublished();
    }

    /**
     * Get the published of the document
     *
     * @param bool $checkPublishTime
     * @return bool
     */
    public function isPublished($checkPublishTime = true)
    {
        $published = true;

        if ($checkPublishTime && $this->publishTime instanceof PublishTime) {
            $published = $this->publishTime->isPublished();
        }

        return ($published && !$this->disabled);
    }

    /**
     * Set the published of the document
     *
     * @param bool $published
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;
        return $this;
    }

    /**
     * Get the disabled of the document
     *
     * @return bool
     */
    public function isDisabled()
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
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        if ($this->metadata === null) {
            $this->metadata = new Metadata();
        }

        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(RegistryInterface $metadata = null)
    {
        if ($metadata !== null && !$metadata instanceof Metadata) {
            $metadata = new Metadata($metadata->toArray());
        }

        $this->metadata = $metadata;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannels(Collection $channels)
    {
        $this->channels->clear();
        $this->channels = new ArrayCollection();

        foreach ($channels as $channel) {
            $this->addChannel($channel); // type check
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels()
    {
        return $this->channels->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(ChannelInterface $channel)
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(ChannelInterface $channel)
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(ChannelInterface $channel)
    {
        $this->channels->removeElement($channel);
        return $this;
    }

    /**
     * @ODM\PreUpdate
     */
    public function updateUpdatedAtOnPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ODM\PrePersist
     * @ODM\PreUpdate
     */
    public function updatePublishTimeOnPreUpdate()
    {
        if (!$this->publishTime instanceof PublishTime) {
            return;
        }

        if (!$this->publishTime->getStartDate() instanceof \DateTime) {
            $this->publishTime->setStartDate($this->createdAt);
        }

        if (!$this->publishTime->getEndDate() instanceof \DateTime) {
            $this->publishTime->setEndDate(new \DateTime(PublishTime::DATE_MAX));
        }
    }
}
