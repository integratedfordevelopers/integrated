<?php
namespace Integrated\Bundle\ContentBundle\Document\Embedded;

use Integrated\Bundle\ContentBundle\Document\Relation\Company;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Embedded document Job
 *
 * @package Integrated\Bundle\ContentBundle\Document\Embedded
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 * @ODM\EmbeddedDocument
 */
class Job
{
    /**
     * @var string
     * @ODM\String
     */
    protected $function;

    /**
     * @var string
     * @ODM\String
     */
    protected $department;

    /**
     * @var Company
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Relation\Company")
     */
    protected $company;

    /**
     * Get the function of the document
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set the function of the document
     *
     * @param string $function
     * @return $this
     */
    public function setFunction($function)
    {
        $this->function = $function;
        return $this;
    }

    /**
     * Get the department of the document
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set the department of the document
     *
     * @param string $department
     * @return $this
     */
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * Get the company of the document
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the company of the document
     *
     * @param Company $company
     * @return $this
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        return $this;
    }
}