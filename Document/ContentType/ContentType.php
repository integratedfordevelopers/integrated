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

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Component\Content\ContentTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * Document ContentType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content_type")
 * @MongoDBUnique(fields="classType")
 */
class ContentType implements ContentTypeInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string The class name of the document type
     * @ODM\String
     * @Assert\NotBlank()
     */
    protected $className;

    /**
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex
     * @Assert\NotBlank()
     */
    protected $classType;

    /**
     * @var Embedded\Field[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Field", strategy="set")
     */
    protected $fields = array();

    /**
     * @var array Embedded\Reference
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Reference", strategy="set")
     */
    protected $references = array();

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
     * Get the className of the document
     *
     * @return string The class name of the document type
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set the className of the document
     *
     * @param string $className The class name of the document type
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    /**
     * Get the classType of the document
     *
     * @return string
     */
    public function getClassType()
    {
        return $this->classType;
    }

    /**
     * Set the classType of the document
     *
     * @param string $classType
     * @return $this
     */
    public function setClassType($classType)
    {
        $this->classType = $classType;
        return $this;
    }

    /**
     * Get the fields of the document
     *
     * @return Embedded\Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getField($name)
    {
        //todo implement
    }

    /**
     * Set the fields of the document
     *
     * @param array $fields Embedded\Field
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Get the references of the document
     *
     * @return array Embedded\Reference
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Set the references of the document
     *
     * @param array $references Embedded\Reference
     * @return $this
     */
    public function setReferences(array $references)
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
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
}