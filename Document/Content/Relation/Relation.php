<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content\Relation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Type;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address;

/**
 * Class for Relations
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\MappedSuperclass
 */
class Relation extends Content
{
    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $accountnumber;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $description;

    /**
     * @var array
     * @ODM\Hash
     */
    protected $phonenumbers = array();

    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="email")
     */
    protected $email;

    /**
     * @var Address[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address", strategy="set")
     */
    protected $addresses = array();

    /**
     * Get the accountnumber of the document
     *
     * @return string
     */
    public function getAccountnumber()
    {
        return $this->accountnumber;
    }

    /**
     * Set the accountnumber of the document
     *
     * @param string $accountnumber
     * @return $this
     */
    public function setAccountnumber($accountnumber)
    {
        $this->accountnumber = $accountnumber;
        return $this;
    }

    /**
     * Get the description of the document
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description of the document
     *
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the phonenumbers of the document
     *
     * @return array
     */
    public function getPhonenumbers()
    {
        return $this->phonenumbers;
    }

    /**
     * Set the phonenumbers of the document
     *
     * @param array $phonenumbers
     * @return $this
     */
    public function setPhonenumbers(array $phonenumbers)
    {
        $this->phonenumbers = $phonenumbers;
        return $this;
    }

    /**
     * Add phonenumber to phonenumbers collection
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addPhonenumber($key, $value)
    {
        $this->phonenumbers[$key] = $value;
        return $this;
    }

    /**
     * Remove phonenumber from phonenumbers collection
     *
     * @param string $key
     * @return mixed The removed element or null if the collection did not contain the element.
     */
    public function removePhonenumber($key)
    {
        if (isset($this->phonenumbers[$key])) {
            $return = $this->phonenumbers[$key];
            unset($this->phonenumbers[$key]);
            return $return;
        }
    }

    /**
     * Get the email of the document
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email of the document
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the addresses of the document
     *
     * @return Address[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set the addresses of the document
     *
     * @param array $addresses
     * @return $this
     */
    public function setAddresses(array $addresses)
    {
        $this->addresses = $addresses;
        return $this;
    }
}