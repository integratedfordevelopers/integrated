<?php
namespace Integrated\Bundle\ContentBundle\Document\Embedded;

use Integrated\Bundle\ContentBundle\Document\Relation\Person;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Author
 *
 * @package Integrated\Bundle\ContentBundle\Document\Embedded
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Author
{
    /**
     * @var string
     * @ODM\String
     */
    protected $type;

    /**
     * @var Person
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Relation\Person")
     */
    protected $person;

    /**
     * Get the type of the document
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of the document
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
     * Get the person of the document
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set the person of the document
     *
     * @param Person $person
     * @return $this
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;
        return $this;
    }
}