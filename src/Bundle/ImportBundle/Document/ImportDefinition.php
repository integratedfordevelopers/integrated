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
use Integrated\Bundle\ImportBundle\Document\Embedded\ImportField;

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
     * @var string
     */
    private $fileId;

    /*
     * @var ImportField[]
     */
    private $fields;

    /**
     * ImportDefition constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
     *
     * @return $this
     */
    public function setExecutedAt(\DateTime $executedAt)
    {
        $this->executedAt = $executedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }

    /**
     * @param string $fileId
     */
    public function setFileId(string $fileId): void
    {
        $this->fileId = $fileId;
    }


    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     *
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

}
