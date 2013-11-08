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

use Integrated\MongoDB\Content\Document\Relation\Company;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CompanyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Company
     */
    private $company;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->company = new Company();
    }

    /**
     * Company should implement ContentInterface
     */
    public function testContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->company);
    }

    /**
     * Company should extend AbstractContent
     */
    public function testAbstractContent()
    {
        $this->assertInstanceOf('Integrated\MongoDB\Content\Document\AbstractContent', $this->company);
    }

    /**
     * Company should extend AbstractRelation
     */
    public function testAbstractRelation()
    {
        $this->assertInstanceOf('Integrated\MongoDB\Content\Document\Relation\AbstractRelation', $this->company);
    }

    /**
     * Test get- and setAccountnumber function
     */
    public function testGetAndSetAccountnumberFunction()
    {
        $accountnumber = 'accountnumber';
        $this->assertEquals($accountnumber, $this->company->setAccountnumber($accountnumber)->getAccountnumber());
    }

    /**
     * Test get- and setDescription function
     */
    public function testGetAndSetDescriptionFunction()
    {
        $description = array('nl' => 'Omschrijving', 'en' => 'Description');
        $this->assertEquals($description, $this->company->setDescription($description)->getDescription());
    }

    /**
     * Test get- and setPhonenumbers function
     */
    public function testGetAndSetPhonenumbersFunction()
    {
        $phonenumbers = array('0123456789', '9876543210');
        $this->assertSame($phonenumbers, $this->company->setPhonenumbers($phonenumbers)->getPhonenumbers());
    }

    /**
     * Test get- and setEmail function
     */
    public function testGetAndSetEmailFunction()
    {
        $email = 'email';
        $this->assertEquals($email, $this->company->setEmail($email)->getEmail());
    }

    /**
     * Test get- and setAddresses function
     */
    public function testGetAndSetAddressesFunction()
    {
        $addresses = array(
            $this->getMock('Integrated\MongoDB\Content\Document\Embedded\Address')
        );
        $this->assertSame($addresses, $this->company->setAddresses($addresses)->getAddresses());
    }

    /**
     * Test get- and setLogo function
     */
    public function testGetAndSetLogoFunction()
    {
        /* @var $logo \Integrated\MongoDB\Content\Document\File | \\PHPUnit_Framework_MockObject_MockObject */
        $logo = $this->getMock('Integrated\MongoDB\Content\Document\File');
        $this->assertSame($logo, $this->company->setLogo($logo)->getLogo());
    }
}