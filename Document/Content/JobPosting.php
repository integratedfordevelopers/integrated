<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Form\Mapping\Annotations as Type;
use Integrated\Bundle\ContentBundle\Document\Content\Relation;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\Document
 * @Type\Document("JobPosting")
 */
class JobPosting extends Article
{
    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $jobTitle;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $salary;

    /**
     * @var string
     * @ODM\String
     * @Type\Field
     */
    protected $applyUrl;

    /**
     * @var Relation\Company
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Relation\Company")
     */
    protected $company;

    /**
     * @var Relation\Person
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Relation\Person")
     */
    protected $contact;

    /**
     * @return string
     */
    public function getJobTitle()
    {
        return $this->jobTitle;
    }

    /**
     * @param string $jobTitle
     * @return $this
     */
    public function setJobTitle($jobTitle)
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param string $salary
     * @return $this
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
        return $this;
    }

    /**
     * @return string
     */
    public function getApplyUrl()
    {
        return $this->applyUrl;
    }

    /**
     * @param string $applyUrl
     * @return $this
     */
    public function setApplyUrl($applyUrl)
    {
        $this->applyUrl = $applyUrl;
        return $this;
    }

    /**
     * @return Relation\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Relation\Company $company
     * @return $this
     */
    public function setCompany(Relation\Company $company)
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return Relation\Person
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param Relation\Person $contact
     * @return $this
     */
    public function setContact(Relation\Person $contact)
    {
        $this->contact = $contact;
        return $this;
    }
}
