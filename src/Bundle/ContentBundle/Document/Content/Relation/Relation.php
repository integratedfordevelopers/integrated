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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Phonenumber;
use Integrated\Common\Content\RankableInterface;
use Integrated\Common\Content\RankTrait;
use Integrated\Common\Form\Mapping\Annotations as Type;

/**
 * Class for Relations.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
abstract class Relation extends Content implements RankableInterface
{
    use RankTrait;

    /**
     * @var string
     * @Type\Field
     */
    protected $accountnumber;

    /**
     * @var string
     * @Type\Field(type="Integrated\Bundle\FormTypeBundle\Form\Type\EditorType")
     */
    protected $description;

    /**
     * @var Phonenumber[]|Collection
     * @Type\Field(
     *      type="Integrated\Bundle\FormTypeBundle\Form\Type\SortableCollectionType",
     *      options={
     *          "entry_type"="Integrated\Bundle\ContentBundle\Form\Type\PhonenumberType",
     *          "allow_add"=true,
     *          "allow_delete"=true
     *      }
     * )
     */
    protected $phonenumbers;

    /**
     * @var string
     * @Type\Field(type="Symfony\Component\Form\Extension\Core\Type\EmailType")
     */
    protected $email;

    /**
     * @var Address[]
     * @Type\Field(
     *      type="Integrated\Bundle\FormTypeBundle\Form\Type\SortableCollectionType",
     *      options={
     *          "entry_type"="Integrated\Bundle\ContentBundle\Form\Type\AddressType",
     *          "default_title"="New address",
     *          "allow_add"=true,
     *          "allow_delete"=true
     *      }
     * )
     */
    protected $addresses;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->phonenumbers = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    /**
     * Get the accountnumber of the document.
     *
     * @return string
     */
    public function getAccountnumber()
    {
        return $this->accountnumber;
    }

    /**
     * Set the accountnumber of the document.
     *
     * @param string $accountnumber
     *
     * @return $this
     */
    public function setAccountnumber($accountnumber)
    {
        $this->accountnumber = $accountnumber;

        return $this;
    }

    /**
     * Get the description of the document.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description of the document.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the phonenumbers of the document.
     *
     * @return Phonenumber[]
     */
    public function getPhonenumbers($type = null)
    {
        if ($type !== null) {
            $result = [];

            foreach ($this->phonenumbers as $obj) {
                if (strcasecmp($type, $obj->getType()) === 0) {
                    $result[] = $obj;
                }
            }

            return $result;
        }

        return $this->phonenumbers;
    }

    /**
     * Set the phonenumbers of the document.
     *
     * @param Phonenumber[] $phonenumbers
     *
     * @return $this
     */
    public function setPhonenumbers(Collection $phonenumbers)
    {
        $this->phonenumbers = $phonenumbers;

        return $this;
    }

    /**
     * Add phonenumber to phonenumbers collection.
     *
     * @param string|Phonenumber $phonenumber
     * @param string             $type
     *
     * @return $this
     */
    public function addPhonenumber($phonenumber, $type = null)
    {
        if ($phonenumber === null) {
            return $this;
        }

        if ($phonenumber instanceof Phonenumber) {
            $obj = $phonenumber;
        } else {
            $obj = new Phonenumber();
            $obj->setNumber($phonenumber);
            $obj->setType($type);
        }

        $this->phonenumbers->add($obj);

        return $this;
    }

    /**
     * Remove phonenumber from phonenumbers collection.
     *
     * @param string|Phonenumber $phonenumber
     *
     * @return bool the removed element or null if the collection did not contain the element
     */
    public function removePhonenumber($phonenumber)
    {
        // @todo (INTEGRATED-452)
        if ($phonenumber instanceof Phonenumber) {
            return $this->phonenumbers->remove($phonenumber);
        }

        $return = false;

        foreach ($this->phonenumbers as $obj) {
            if (strcasecmp($phonenumber, $obj->getNumber()) === 0) {
                $return = $this->phonenumbers->remove($obj);
            }
        }

        return $return;
    }

    /**
     * Get the email of the document.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email of the document.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the addresses of the document.
     *
     * @return Address[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set the addresses of the document.
     *
     * @param Collection $addresses
     *
     * @return $this
     */
    public function setAddresses(Collection $addresses)
    {
        $this->addresses = $addresses;

        return $this;
    }

    /**
     * Add address to addresses collection.
     *
     * @param Address $address
     *
     * @return $this
     */
    public function addAddress(Address $address = null)
    {
        if ($address !== null) {
            $this->addresses->add($address);
        }

        return $this;
    }

    /**
     * @param Address $address
     *
     * @return bool
     */
    public function removeAddress(Address $address)
    {
        return $this->addresses->removeElement($address);
    }
}
