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
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Metadata;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\PublishTime;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\ConnectorInterface;
use Integrated\Common\Content\ConnectorTrait;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Content\Embedded\RelationInterface;
use Integrated\Common\Content\ExtensibleInterface;
use Integrated\Common\Content\ExtensibleTrait;
use Integrated\Common\Content\MetadataInterface;
use Integrated\Common\Content\PublishableInterface;
use Integrated\Common\Content\PublishTimeInterface;
use Integrated\Common\Content\RegistryInterface;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Abstract base class for document types.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
abstract class Content implements ContentInterface, ExtensibleInterface, MetadataInterface, ChannelableInterface, PublishableInterface, ConnectorInterface
{
    use ConnectorTrait;
    use ExtensibleTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     * @Slug(fields={"id"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var string the type of the ContentType
     */
    protected $contentType;

    /**
     * @var ArrayCollection
     */
    protected $relations;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var PublishTime
     * @Type\Field(type="Integrated\Bundle\ContentBundle\Form\Type\PublishTimeType")
     */
    protected $publishTime;

    /**
     * @var bool
     */
    protected $published = true;

    /**
     * @var bool
     * @Type\Field(
     *     type="Symfony\Component\Form\Extension\Core\Type\CheckboxType",
     *     options={"attr"={"align_with_widget"=true}}
     * )
     */
    protected $disabled = false;

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var Collection
     */
    protected $channels;

    /**
     * @var Channel
     */
    protected $primaryChannel;

    /**
     * @var Embedded\CustomFields
     */
    protected $customFields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->relations = new ArrayCollection();
        $this->updatedAt = new \DateTime();
        $this->publishTime = new PublishTime();
        $this->channels = new ArrayCollection();
        $this->connectors = new ArrayCollection();
    }

    /**
     * Get the id of the document.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the document.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the slug of the document.
     *
     * @return string
     */
    public function getSlug()
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
    public function setSlug($slug)
    {
        $this->slug = $slug;

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
        //should always be instanceOf collection, but due to corrupt database can sometimes be null
        if (!$this->relations instanceof Collection) {
            $this->relations = new ArrayCollection();
        }

        return $this->relations;
    }

    /**
     * {@inheritdoc}
     */
    public function setRelations(Collection $relations)
    {
        foreach ($relations as $relation) {
            if ($relation instanceof RelationInterface) {
                $this->addRelation($relation);
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRelation(RelationInterface $relation)
    {
        if ($exist = $this->getRelation($relation->getRelationId())) {
            $exist->addReferences($relation->getReferences());
        } else {
            $this->getRelations()->add($relation);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRelation(RelationInterface $relation)
    {
        $this->getRelations()->removeElement($relation);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation($relationId)
    {
        return $this->getRelations()->filter(function ($relation) use ($relationId) {
            if ($relation instanceof RelationInterface) {
                if ($relation->getRelationId() == $relationId) {
                    return true;
                }
            }

            return false;
        })->first();
    }

    /**
     * @param $relationType
     *
     * @return ArrayCollection|false
     */
    public function getRelationsByRelationType($relationType)
    {
        return $this->getRelations()->filter(function ($relation) use ($relationType) {
            if ($relation instanceof RelationInterface) {
                if ($relation->getRelationType() == $relationType) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param $relationType
     *
     * @return array|bool
     */
    public function getReferencesByRelationType($relationType)
    {
        if ($relations = $this->getRelationsByRelationType($relationType)) {
            $references = [];

            /** @var RelationInterface $relation */
            foreach ($relations as $relation) {
                $references = array_merge($references, $relation->getReferences()->toArray());
            }

            return $references;
        }

        return false;
    }

    /**
     * @param array $relationTypes
     *
     * @return array|bool
     */
    public function getReferencesByRelationTypes(array $relationTypes)
    {
        $references = [];
        foreach ($relationTypes as $relationType) {
            $references = array_merge($references, $this->getReferencesByRelationType($relationType));
        }

        if (\count($references) > 0) {
            return $references;
        }

        return false;
    }

    /**
     * @param $relationType
     *
     * @return Content|null
     */
    public function getReferenceByRelationType($relationType)
    {
        $references = $this->getReferencesByRelationType($relationType);

        if (\is_array($references) && \count($references)) {
            return $references[0];
        }

        return null;
    }

    /**
     * @param string $relationId
     * @param bool   $published
     *
     * @return ArrayCollection
     */
    public function getReferencesByRelationId($relationId, $published = true)
    {
        foreach ($this->getRelations() as $relation) {
            if ($relation instanceof RelationInterface) {
                if ($relation->getRelationId() == $relationId) {
                    if ($references = $relation->getReferences()) {
                        if (true !== $published) {
                            return $references;
                        }

                        return $references->filter(function ($content) {
                            return $content instanceof self ? $content->isPublished() : true;
                        });
                    }
                }
            }
        }

        return new ArrayCollection();
    }

    /**
     * @param string $relationId
     * @param bool   $published
     *
     * @return Content|null
     */
    public function getReferenceByRelationId($relationId, $published = true)
    {
        if ($references = $this->getReferencesByRelationId($relationId, $published)) {
            return $references->first();
        }

        return null;
    }

    /**
     * Get the createdAt of the document.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the document.
     *
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the updatedAt of the document.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the updatedAt of the document.
     *
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishTime(): PublishTimeInterface
    {
        return $this->publishTime;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishTime(PublishTimeInterface $publishTime)
    {
        $this->publishTime = $publishTime;

        return $this;
    }

    /**
     * Get the published of the document.
     *
     * @deprecated
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->isPublished();
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished($checkPublishTime = true): bool
    {
        $published = true;

        if ($checkPublishTime && $this->publishTime instanceof PublishTime) {
            $published = $this->publishTime->isPublished();
        }

        return $published && !$this->disabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get the disabled of the document.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set the disabled of the document.
     *
     * @param bool $disabled
     *
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
        if (null === $this->metadata) {
            $this->metadata = new Metadata();
        }

        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(RegistryInterface $metadata = null)
    {
        if (null !== $metadata && !$metadata instanceof Metadata) {
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

        if (null === $this->primaryChannel) {
            $this->setPrimaryChannel($channel);
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
     * @return Channel|null
     */
    public function getPrimaryChannel()
    {
        if (null === $this->primaryChannel && $this->channels->count()) {
            return $this->channels->first();
        }

        return $this->primaryChannel;
    }

    /**
     * @param ChannelInterface|null $primaryChannel
     *
     * @return $this
     */
    public function setPrimaryChannel(ChannelInterface $primaryChannel = null)
    {
        $this->primaryChannel = $primaryChannel;

        return $this;
    }

    /**
     * @return Embedded\CustomFields
     */
    public function getCustomFields()
    {
        if (null === $this->customFields) {
            $this->customFields = new Embedded\CustomFields();
        }

        return $this->customFields;
    }

    /**
     * @param RegistryInterface|null $customFields
     *
     * @return $this
     */
    public function setCustomFields(RegistryInterface $customFields = null)
    {
        if (null !== $customFields && !$customFields instanceof Embedded\CustomFields) {
            $customFields = new Embedded\CustomFields($customFields->toArray());
        }

        $this->customFields = $customFields;

        return $this;
    }

    /**
     * updateUpdatedAtOnPreUpdate.
     */
    public function updateUpdatedAtOnPreUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * updatePublishTimeOnPreUpdate.
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
            $this->publishTime->setEndDate(new \DateTime(PublishTimeInterface::DATE_MAX));
        }
    }

    /**
     * @return string
     */
    abstract public function __toString();
}
