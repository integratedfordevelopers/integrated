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
use Integrated\Common\ContentType\Mapping\Annotations as Type;
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
     * @Type\Field(type="choice", options={"choices"={"Male", "Female"}})
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
    protected $firstname;

    /**
     * @var string
     * @ODM\String
     * @Type\Field(options={"label"="Last name"})
     */
    protected $lastname;

    /**
     * @var Collection Job[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job", strategy="set")
     */
    protected $jobs;

    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\File")
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
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the firstname of the document
     *
     * @param string $firstname
     * @return $this
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get the lastname of the document
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the lastname of the document
     *
     * @param string $lastname
     * @return $this
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
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
     * @param Job $job
     * @return $this
     */
    public function addJob(Job $job)
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs->add($job);
        }
        return $this;
    }

    /**
     * Remove job from jobs collection
     *
     * @param Job $job
     * @return bool true if this collection contained the specified element, false otherwise.
     */
    public function removeJob(Job $job)
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
}