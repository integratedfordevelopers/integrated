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
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\SlugBundle\Mapping\Annotations\Slug;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job;

/**
 * Document type Relation\Person
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("Person")
 */
class Person extends Relation
{
    /**
     * @var string
     * @ODM\String
     * @Type\Field(type="choice", options={"choices"={"Male"="Male", "Female"="Female"}})
     */
    protected $gender;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $prefix;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $nickname;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(options={"label"="First name"})
     */
    protected $firstName;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(options={"label"="Last name"})
     */
    protected $lastName;

    /**
     * @var string
     * @ODM\String
     * @ODM\UniqueIndex(sparse=true)
     * @Slug(fields={"firstName", "lastName"})
     * @Type\Field
     */
    protected $slug;

    /**
     * @var Collection Job[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job")
     */
    protected $jobs;

    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\File")
     * @Type\Field(type="integrated_image_choice")
     */
    protected $picture;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->jobs = new ArrayCollection();
    }

    /**
     * Get the gender of the document
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set the gender of the document
     *
     * @param string $gender
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Get the prefix of the document
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the prefix of the document
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Get the nickname of the document
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set the nickname of the document
     *
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
        return $this;
    }

    /**
     * Get the firstname of the document
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the firstname of the document
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get the lastName of the document
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the lastName of the document
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get the slug of the document
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the slug of the document
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get the jobs of the document
     *
     * @return Job[]
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Set the jobs of the document
     *
     * @param Collection $jobs
     * @return $this
     */
    public function setJobs(Collection $jobs)
    {
        $this->jobs = $jobs;
        return $this;
    }

    /**
     * Add job to the jobs collection
     *
     * @param mixed $job
     * @return $this
     */
    public function addJob($job)
    {
        if ($job instanceof Job) {
            if (!$this->jobs->contains($job)) {
                $this->jobs->add($job);
            }
        }
        return $this;
    }

    /**
     * Remove job from jobs collection
     *
     * @param mixed $job
     * @return bool true if this collection contained the specified element, false otherwise.
     */
    public function removeJob($job)
    {
        return $this->jobs->removeElement($job);
    }

    /**
     * Get the picture of the document
     *
     * @return File
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the picture of the document
     *
     * @param File $picture
     * @return $this
     */
    public function setPicture(File $picture)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * Get the relative cover image URL for person (picture)
     *
     * @return string
     */
    public function getCover()
    {
        if ($this->getPicture()) {
          return $this->getPicture()->getWebPath();
        }
        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}