<?php
namespace Integrated\Bundle\ContentBundle\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Address
 *
 * @package Integrated\Bundle\ContentBundle\Document\Embedded
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Address
{
    /**
     * @var string
     * @ODM\String
     */
    protected $type;

    /**
     * @var string
     * @ODM\String
     */
    protected $address1;

    /**
     * @var string
     * @ODM\String
     */
    protected $address2;

    /**
     * @var string
     * @ODM\String
     */
    protected $zipcode;

    /**
     * @var string
     * @ODM\String
     */
    protected $city;

    /**
     * @var string
     * @ODM\String
     */
    protected $state;

    /**
     * @var string
     * @ODM\String
     */
    protected $country;

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
     * Get the address1 of the document
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * Set the address1 of the document
     *
     * @param string $address1
     * @return $this
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * Get the address2 of the document
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Set the address2 of the document
     *
     * @param string $address2
     * @return $this
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * Get the zipcode of the document
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Set the zipcode of the document
     *
     * @param string $zipcode
     * @return $this
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    /**
     * Get the city of the document
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set the city of the document
     *
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * Get the state of the document
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the state of the document
     *
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get the country of the document
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set the country of the document
     *
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
}