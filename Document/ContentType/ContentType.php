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

/**
 * Document ContentType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="contenttype")
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
     */
    protected $type;

    /**
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex
     */
    protected $name;

    /**
     * @var array Embedded\Field
     * @ODM\EmbedMany(targetDocument="FMS\Bundle\ContentTypeBundle\Document\Embedded\Field", strategy="set")
     */
    protected $fields = array();

    /**
     * @var array Embedded\Reference
     * @ODM\EmbedMany(targetDocument="FMS\Bundle\ContentTypeBundle\Document\Embedded\Reference", strategy="set")
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
     * Get the type of the document
     *
     * @return string The class name of the document type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the document
     *
     * @param string $type The class name of the document type
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
     * Get the fields of the document
     *
     * @return array Embedded\Field
     */
    public function getFields()
    {
        return $this->fields;
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