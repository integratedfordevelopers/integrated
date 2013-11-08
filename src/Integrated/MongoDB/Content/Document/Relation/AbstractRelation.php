<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\MongoDB\Content\Document\Relation;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Common\ContentType\Mapping\Annotations as Content;
use Integrated\MongoDB\Content\Document\AbstractContent;
use Integrated\MongoDB\Content\Document\Embedded\Address;

/**
 * Abstract class for Relations
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\MappedSuperclass
 */
abstract class AbstractRelation extends AbstractContent
{
    /**
     * @var string
     * @ODM\String
     */
    protected $accountnumber;

    /**
     * @var string
     * @ODM\String
     * @Content\Field(label="Description", type="textarea")
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
     * @Content\Field(label="E-mailaddress", type="email")
     */
    protected $email;

    /**
     * @var Address[]
     * @ODM\EmbedMany(targetDocument="Integrated\MongoDB\Content\Document\Embedded\Address", strategy="set")
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