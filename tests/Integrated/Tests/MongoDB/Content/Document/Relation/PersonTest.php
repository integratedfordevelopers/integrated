<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\Content\Document\Relation;

use Integrated\MongoDB\Content\Document\Relation\Person;

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
    public function testContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->person);
    }

    /**
     * Person should extend AbstractContent
     */
    public function testAbstractContent()
    {
        $this->assertInstanceOf('Integrated\MongoDB\Content\Document\AbstractContent', $this->person);
    }

    /**
     * Person should extend AbstractRelation
     */
    public function testAbstractRelation()
    {
        $this->assertInstanceOf('Integrated\MongoDB\Content\Document\Relation\AbstractRelation', $this->person);
    }

    /**
     * Test get- and setSex function
     */
    public function testGetAndSetSexFunction()
    {
        $sex = 'sex';
        $this->assertEquals($sex, $this->person->setSex($sex)->getSex());
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
    public function testGetAndSetLastnameFunction()
    {
        $lastname = 'de Vries';
        $this->assertEquals($lastname, $this->person->setLastname($lastname)->getLastname());
    }

    /**
     * Test get- and setJobs function
     */
    public function testGetAndSetJobsFunction()
    {
        $jobs = array(
            $this->getMock('Integrated\MongoDB\Content\Document\Embedded\Job')
        );
        $this->assertSame($jobs, $this->person->setJobs($jobs)->getJobs());
    }

    /**
     * Test get- and setPicture function
     */
    public function testGetAndSetPictureFunction()
    {
        /* @var $picture \Integrated\MongoDB\Content\Document\File | \PHPUnit_Framework_MockObject_MockObject */
        $picture = $this->getMock('Integrated\MongoDB\Content\Document\File');
        $this->assertSame($picture, $this->person->setPicture($picture)->getPicture());

    }
}