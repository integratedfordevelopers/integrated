<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ImportBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Bundle\ImportBundle\Document\Embedded\ImportField;
use Integrated\Common\Channel\ChannelInterface;

class ImportDefinition
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $executedAt;

    /**
     * @var Collection
     */
    protected $channels;

    /**
     * @var string
     */
    private $fileId;

    /*
     * @var ImportField[]
     */
    private $fields;

    /**
     * @var string
     */
    private $imageBaseUrl;

    /**
     * @var string
     */
    private $imageContentType;

    /**
     * @var Relation
     */
    private $imageRelation;

    /**
     * @var string
     */
    private $fileContentType;

    /**
     * @var Relation
     */
    private $fileRelation;

    /**
     * ImportDefition constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->channels = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string | null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getExecutedAt()
    {
        return $this->executedAt;
    }

    /**
     * @param \DateTime $executedAt
     */
    public function setExecutedAt(\DateTime $executedAt)
    {
        $this->executedAt = $executedAt;
    }

    /**
     * @param Collection $channels
     */
    public function setChannels(Collection $channels)
    {
        $this->channels->clear();
        $this->channels = new ArrayCollection();

        foreach ($channels as $channel) {
            $this->addChannel($channel);
        }
    }

    /**
     * @return ChannelInterface[]
     */
    public function getChannels()
    {
        return $this->channels->toArray();
    }

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel)
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    public function hasChannel(ChannelInterface $channel)
    {
        return $this->channels->contains($channel);
    }

    /**
     * @param ChannelInterface $channel
     */
    public function removeChannel(ChannelInterface $channel)
    {
        $this->channels->removeElement($channel);
    }

    /**
     * @return string
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }

    /**
     * @param null | string $fileId
     */
    public function setFileId(?string $fileId): void
    {
        $this->fileId = $fileId;
    }

    /**
     * @return ImportField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return ImportField
     */
    public function getField($name)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasField($name)
    {
        foreach ($this->getFields() as $field) {
            if ($field->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the fields of the import definition.
     *
     * @param ImportField[] $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return string
     */
    public function getImageBaseUrl(): ?string
    {
        return $this->imageBaseUrl;
    }

    /**
     * @param string $imageBaseUrl
     */
    public function setImageBaseUrl(string $imageBaseUrl)
    {
        $this->imageBaseUrl = $imageBaseUrl;
    }

    /**
     * @return string
     */
    public function getImageContentType(): ?string
    {
        return $this->imageContentType;
    }

    /**
     * @param string $imageContentType
     */
    public function setImageContentType(?string $imageContentType)
    {
        $this->imageContentType = $imageContentType;
    }

    /**
     * @return Relation
     */
    public function getImageRelation()
    {
        return $this->imageRelation;
    }

    /**
     * @param Relation $imageRelation
     */
    public function setImageRelation($imageRelation)
    {
        $this->imageRelation = $imageRelation;
    }

    /**
     * @return string
     */
    public function getFileContentType(): ?string
    {
        return $this->fileContentType;
    }

    /**
     * @param string $fileContentType
     */
    public function setFileContentType(?string $fileContentType): void
    {
        $this->fileContentType = $fileContentType;
    }

    /**
     * @return Relation|null
     */
    public function getFileRelation(): ?Relation
    {
        return $this->fileRelation;
    }

    /**
     * @param Relation $fileRelation
     */
    public function setFileRelation(?Relation $fileRelation): void
    {
        $this->fileRelation = $fileRelation;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
