<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content\Relation;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class PersonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Person
     */
    private $person;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->person = new Person();
    }

    /**
     * Person should implement ContentInterface
     */
    public function testInstanceOfContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->person);
    }

    /**
     * Person should extend Content
     */
    public function testInstanceOfContent()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\Content', $this->person);
    }

    /**
     * Person should extend Relation
     */
    public function testInstanceOfRelation()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation', $this->person);
    }

    /**
     * Test get- and setGender function
     */
    public function testGetAndSetGenderFunction()
    {
        $gender = 'male';
        $this->assertEquals($gender, $this->person->setGender($gender)->getGender());
    }

    /**
     * Test get- and setNickname function
     */
    public function testGetAndSetNicknameFunction()
    {
        $nickname = 'nickname';
        $this->assertEquals($nickname, $this->person->setNickname($nickname)->getNickname());
    }

    /**
     * Test get- and setPrefix function
     */
    public function testGetAndSetPrefixFunction()
    {
        $prefix = 'Nutty Professor';
        $this->assertEquals($prefix, $this->person->setPrefix($prefix)->getPrefix());
    }

    /**
     * Test get- and setFirstname function
     */
    public function testGetAndSetFirstnameFunction()
    {
        $firstname = 'Henk';
        $this->assertEquals($firstname, $this->person->setFirstname($firstname)->getFirstname());
    }

    /**
     * Test get- and setLastname function
     */
    public function testGetAndSetLastNameFunction()
    {
        $lastName = 'de Vries';
        $this->assertEquals($lastName, $this->person->setLastName($lastName)->getLastName());
    }

    /**
     * Test get- and setJobs function
     */
    public function testGetAndSetJobsFunction()
    {
        $jobs = new ArrayCollection(
            array(
                $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job')
            )
        );
        $this->assertSame($jobs, $this->person->setJobs($jobs)->getJobs());
    }

    /**
     * Test addJob function
     */
    public function testAddJobFunction()
    {
        /* @var $job \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job | \PHPUnit_Framework_MockObject_MockObject */
        $job = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job');

        // Asserts
        $this->assertSame($this->person, $this->person->addJob($job));
        $this->assertCount(1, $this->person->getJobs());
    }

    /**
     * Test addJob function with duplicate job
     */
    public function testAddJobFunctionWithDuplicateJob()
    {
        /* @var $job \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job | \PHPUnit_Framework_MockObject_MockObject */
        $job = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job');

        // Add job two times
        $this->person->addJob($job)->addJob($job);

        // Asserts
        $this->assertCount(1, $this->person->getJobs());
    }

    /**
     * Test removeJob function
     */
    public function testRemoveJobFunction()
    {
        /* @var $job \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job | \PHPUnit_Framework_MockObject_MockObject */
        $job = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job');

        // Add author
        $this->person->addJob($job);

        // Assert
        $this->assertTrue($this->person->removeJob($job));
    }

    /**
     * Test removeJob function with unknown job
     */
    public function testRemoveAuthorFunctionWithUnknownAuthor()
    {
        /* @var $job \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author | \PHPUnit_Framework_MockObject_MockObject */
        $job = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Job');

        // Assert
        $this->assertFalse($this->person->removeJob($job));
    }

    /**
     * Test get- and setPicture function
     */
    public function testGetAndSetPictureFunction()
    {
        /* @var $picture \Integrated\Bundle\ContentBundle\Document\Content\File | \PHPUnit_Framework_MockObject_MockObject */
        $picture = $this->getMock('Integrated\Bundle\ContentBundle\Document\Content\File');
        $this->assertSame($picture, $this->person->setPicture($picture)->getPicture());
    }


    /**
     * Test toString function with first and last name
     */
    public function testToStringFunctionWithFirstAndLastName()
    {
        $firstName = 'Henk';
        $lastName = 'de Vries';

        $this->person->setFirstName($firstName)->setLastName($lastName);

        $this->assertEquals($firstName . ' ' . $lastName, (string) $this->person);
    }

    /**
     * Test toString function with first name only
     */
    public function testToStringWithFirstNameOnly()
    {
        $firstName = 'Henk';
        $this->assertEquals($firstName, (string) $this->person->setFirstName($firstName));
    }

    /**
     * Test toString function with last name only
     */
    public function testToStringWithLastNameOnly()
    {
        $lastName = 'de Vries';
        $this->assertEquals($lastName, (string) $this->person->setLastName($lastName));
    }
}