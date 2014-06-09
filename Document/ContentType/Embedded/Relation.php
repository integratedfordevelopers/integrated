<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\ContentType\Embedded;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Common\ContentType\ContentTypeRelationInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

/**
 * Embedded document Relation
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Relation implements ContentTypeRelationInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string The name of the Relation
     * @ODM\String
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string The type of the Relation
     * @ODM\String
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @var ContentType[]
     * @ODM\ReferenceMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType")
     * @Assert\NotBlank()
     */
    protected $contentTypes;

    /**
     * @var bool One or more references possible
     * @ODM\Boolean
     * @Assert\NotBlank()
     */
    protected $multiple;

    /**
     * @var bool Is the reference required
     * @ODM\Boolean
     */
    protected $required;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contentTypes = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
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
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * @param Collection $contentTypes
     * @return $this
     */
    public function setContentTypes(Collection $contentTypes)
    {
        $this->contentTypes = $contentTypes;
        return $this;
    }

    /**
     * @param ContentType $contentType
     * @return $this
     */
    public function addContentType($contentType)
    {
        if (!$this->contentTypes->contains($contentType)) {
            $this->contentTypes->add($contentType);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param bool $multiple
     * @return $this
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     * @return $this;
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }
}