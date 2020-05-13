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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class AddressTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Address
     */
    private $address;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->address = new Address();
    }

    /**
     * Test get- and setType function.
     */
    public function testGetAndSetTypeFunction()
    {
        $type = 'type';
        $this->assertEquals($type, $this->address->setType($type)->getType());
    }

    /**
     * Test get- and setAddress1 function.
     */
    public function testGetAndSetAddress1Function()
    {
        $address1 = 'address1';
        $this->assertEquals($address1, $this->address->setAddress1($address1)->getAddress1());
    }

    /**
     * Test get- and setAddress2 function.
     */
    public function testGetAndSetAddress2Function()
    {
        $address2 = 'address2';
        $this->assertEquals($address2, $this->address->setAddress2($address2)->getAddress2());
    }

    /**
     * Test get- and setZipcode function.
     */
    public function testGetAndSetZipcodeFunction()
    {
        $zipcode = 'zipcode';
        $this->assertEquals($zipcode, $this->address->setZipcode($zipcode)->getZipcode());
    }

    /**
     * Test get- and setCity function.
     */
    public function testGetAndSetCityFunction()
    {
        $city = 'city';
        $this->assertEquals($city, $this->address->setCity($city)->getCity());
    }

    /**
     * Test get- and setState function.
     */
    public function testGetAndSetStateFunction()
    {
        $state = 'state';
        $this->assertEquals($state, $this->address->setState($state)->getState());
    }

    /**
     * Test get- and setCountry function.
     */
    public function testGetAndSetCountryFunction()
    {
        $country = 'city';
        $this->assertEquals($country, $this->address->setCountry($country)->getCountry());
    }
}
