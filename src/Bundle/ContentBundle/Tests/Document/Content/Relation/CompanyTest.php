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
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CompanyTest extends RelationTest
{
    /**
     * @var Company
     */
    private $company;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->company = new Company();
    }

    /**
     * Test get- and setEmail function.
     */
    public function testGetAndSetEmailFunction()
    {
        $email = 'email';
        $this->assertEquals($email, $this->company->setEmail($email)->getEmail());
    }

    /**
     * Test get- and setWebsite function.
     */
    public function testGetAndSetWebsiteFunction()
    {
        $website = 'http://www.website.com';
        $this->assertEquals($website, $this->company->setWebsite($website)->getWebsite());
    }

    /**
     * Test get- and setAddresses function.
     */
    public function testGetAndSetAddressesFunction()
    {
        $addresses = new ArrayCollection([
            $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address'),
        ]);
        $this->assertSame($addresses, $this->company->setAddresses($addresses)->getAddresses());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertEquals($name, $this->company->setName($name)->getName());
    }

    /**
     * Test get- and setLogo function.
     */
    public function testGetAndSetLogoFunction()
    {
        /* @var $logo \Integrated\Common\Content\Document\Storage\Embedded\StorageInterface | \PHPUnit_Framework_MockObject_MockObject */
        $logo = $this->createMock('Integrated\Common\Content\Document\Storage\Embedded\StorageInterface');
        $this->assertSame($logo, $this->company->setLogo($logo)->getLogo());
    }

    /**
     * Test toString function.
     */
    public function testToStringFunction()
    {
        $name = 'Name';
        $this->assertEquals($name, (string) $this->company->setName($name));
    }

    /**
     * {@inheritdoc}
     */
    protected function getContent()
    {
        return $this->company;
    }
}
