<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;

/**
 * Embedded document Author
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Author
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Person
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
