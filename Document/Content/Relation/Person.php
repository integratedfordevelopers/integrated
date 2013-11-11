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
use Integrated\Common\ContentType\Mapping\Annotations as Content;
use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job;

/**
 * Document type Relation\Person
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 * @Content\Document("Person")
 */
class Person extends AbstractRelation
{
    /**
     * @var string
     * @ODM\String
     * @Content\Field(type="choice", options={"choices"={"Male", "Female"}})
     */
    protected $sex;

    /**
     * @var string
     * @ODM\String
     * @content\Field
     */
    protected $prefix;

    /**
     * @var string
     * @ODM\String
     * @Content\Field
     */
    protected $nickname;

    /**
     * @var string
     * @ODM\String
     * @Content\Field
     */
    protected $firstname;

    /**
     * @var string
     * @ODM\String
     * @Content\Field
     */
    protected $lastname;

    /**
     * @var Job[]
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job", strategy="set")
     */
    protected $jobs = array();

    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\File")
     */
    protected $picture;

    /**
     * Get the sex of the document (nothing more!)
     *
     * @return string
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set the sex of the document
     *
     * @param string $sex
     * @return $this
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
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
     * @param array $jobs
     * @return $this
     */
    public function setJobs(array $jobs)
    {
        $this->jobs = $jobs;
        return $this;
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