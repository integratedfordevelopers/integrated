<?php
namespace Integrated\Bundle\ContentBundle\Document\Relation;

use Integrated\Bundle\ContentBundle\Document\AbstractContent;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Abstract class for Relations
 *
 * @package Integrated\Bundle\ContentBundle\Document\Relation
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
     */
    protected $email;

    /**
     * @var array Address
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Embedded\Address", strategy="set")
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
     * @return array
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