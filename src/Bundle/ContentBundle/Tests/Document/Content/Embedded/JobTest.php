<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content\Embedded;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class JobTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Job
     */
    private $job;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->job = new Job();
    }

    /**
     * Test get- and setFunction function.
     */
    public function testGetAndSetFunctionFunction()
    {
        $function = 'function';
        $this->assertEquals($function, $this->job->setFunction($function)->getFunction());
    }

    /**
     * Test get- and setDepartment function.
     */
    public function testGetAndSetDepartmentFunction()
    {
        $department = 'department';
        $this->assertEquals($department, $this->job->setDepartment($department)->getDepartment());
    }

    /**
     * Test get- and setCompany function.
     */
    public function testGetAndSetCompanyFunction()
    {
        /* @var $company \Integrated\Bundle\ContentBundle\Document\Content\Relation\Company | \PHPUnit_Framework_MockObject_MockObject */
        $company = $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Relation\Company');
        $this->assertSame($company, $this->job->setCompany($company)->getCompany());
    }
}
