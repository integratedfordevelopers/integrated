<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;

/**
 * Embedded document Job.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class Job
{
    /**
     * @var string
     */
    protected $function;

    /**
     * @var string
     */
    protected $department;

    /**
     * @var Company
     */
    protected $company;

    /**
     * Get the function of the document.
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set the function of the document.
     *
     * @param string $function
     *
     * @return $this
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get the department of the document.
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set the department of the document.
     *
     * @param string $department
     *
     * @return $this
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get the company of the document.
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set the company of the document.
     *
     * @param Company $company
     *
     * @return $this
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;

        return $this;
    }
}
