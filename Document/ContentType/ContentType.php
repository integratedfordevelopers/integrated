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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Common\ContentType\ContentTypeRelationInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * Document ContentType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content_type", repositoryClass="ContentTypeRepository")
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
    protected $fields = [];

    /**
     * @var Embedded\Relation[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation")
     */
    protected $relations;

	/**
	 * @var mixed[]
	 * @ODM\Hash
	 */
	protected $options = [];

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
        $this->relations = new ArrayCollection();
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
     * @param Collection $relations
     * @return $this
     */
    public function setRelations(Collection $relations)
    {
        $this->relations = $relations;
        return $this;
    }

    /**
     * @param ContentTypeRelationInterface $relation
     * @return bool TRUE if Relation is added FALSE otherwise
     */
    public function addRelation(ContentTypeRelationInterface $relation)
    {
        if ($this->hasRelation($relation)) {
            return false;
        }

        return $this->relations->add($relation);
    }

    /**
     * @param ContentTypeRelationInterface $relation
     * @return bool TRUE if Relation is removed FALSE otherwise
     */
    public function removeRelation(ContentTypeRelationInterface $relation)
    {
        if (!$this->hasRelation($relation)) {
            return false;
        }

        return $this->relations->removeElement($relation);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelation($id)
    {
        foreach ($this->getRelations() as $relation) {
            if ($relation->getId() == $id) {
                return $relation;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRelation(ContentTypeRelationInterface $relation)
    {
        /** @var $item ContentTypeRelationInterface */
        foreach ($this->getRelations() as $item) {
            if ($item->getId() == $relation->getId()) {
                return true;
            }
        }

        return false;
    }

	/**
	 * @inheritdoc
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Overrider all the option with a new set of values for this content type
	 *
	 * @param string[] $options
	 * @return $this
	 */
	public function setOptions(array $options)
	{
		$this->options = [];

		foreach ($options as $name => $value) {
			$this->setOption($name, $value);
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function getOption($name)
	{
		if (isset($this->options[$name])) {
			return $this->options[$name];
		}

		return null;
	}

	/**
	 * Set the value of the specified key.
	 *
	 * @param string $name
	 * @param null | mixed $value
	 * @return $this
	 */
	public function setOption($name, $value = null)
	{
		if ($value === null) {
			unset($this->options[$name]);
		} else {
			$this->options[$name] = $value;
		}

		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function hasOption($name)
	{
		return isset($this->options[$name]);
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
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}