<?php
namespace Integrated\Bundle\ContentBundle\Document\Relation;

use Integrated\Bundle\ContentBundle\Document\File;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Document type Relation\Person
 *
 * @package Integrated\Bundle\ContentBundle\Document\Relation
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\Document(collection="content")
 */
class Person extends AbstractRelation
{
    /**
     * @var string
     * @ODM\String
     */
    protected $sex;

    /**
     * @var string
     * @ODM\String
     */
    protected $nickname;

    /**
     * @var string
     * @ODM\String
     */
    protected $surname;

    /**
     * @var string
     * @ODM\String
     */
    protected $title;

    /**
     * @var array Job
     * @ODM\EmbedMany(targetDocument="Integrated\Bundle\ContentBundle\Document\Embedded\Job", strategy="set")
     */
    protected $jobs = array();

    /**
     * @var File
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\File")
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
     * Get the surname of the document
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set the surname of the document
     *
     * @param string $surname
     * @return $this
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * Get the title of the document
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title of the document
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the jobs of the document
     *
     * @return array
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