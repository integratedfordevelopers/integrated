<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\ContentType;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * Document ContentType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content_type")
 * @MongoDBUnique(fields="type")
 */
class ContentType implements ContentTypeInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string The class of the content type
     * @ODM\String
     * @Assert\NotBlank()
     */
    protected $class;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex
     */
    protected $type;

    /**
     * @var Embedded\Field[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field")
     */
    protected $fields = array();

    /**
     * @var Embedded\Relation[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation")
     */
    protected $relations = array();

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $instance = new $this->class();
        $instance->setContentType($this->type);

        return $instance;
    }

    /**
     * Get the id of the content type
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the content type
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
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set the class of the content type
     *
     * @param string $class The class of the content type
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
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
     * Set the name of content type
     *
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        // TODO use sluggable extension
        if (null === $this->type) {
            $this->setType(trim(strtolower(str_replace(' ', '_', $this->name))));
        }

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
     * Set the type of the content type
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
     * Set the fields of the content type
     *
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
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
     * Set the relations of the content type
     *
     * @param array $relations
     * @return $this
     */
    public function setRelations(array $relations)
    {
        $this->relations = $relations;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation($class, $type = null)
    {
        // TODO: Implement getRelation() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasRelation($class, $type = null)
    {
        // TODO: Implement hasRelation() method.
    }

    /**
     * Get the createdAt of the content type
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the content type
     *
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}