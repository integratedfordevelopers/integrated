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

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Relation
     */
    private $relation;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->relation = new Relation();
    }

    /**
     * Relation should implement ContentInterface
     */
    public function testInstanceOfContentInterface()
    {
        $this->assertInstanceOf('Integrated\Common\Content\ContentInterface', $this->relation);
    }

    /**
     * Test get- and setAccountnumber function
     */
    public function testGetAndSetAccountnumberFunction()
    {
        $accountnumber = 'accountnumber';
        $this->assertEquals($accountnumber, $this->relation->setAccountnumber($accountnumber)->getAccountnumber());
    }

    /**
     * Test get- and setDescription function
     */
    public function testGetAndSetDescriptionFunction()
    {
        $description = 'description';
        $this->assertEquals($description, $this->relation->setDescription($description)->getDescription());
    }

    /**
     * Test get- and setPhonenumbers function
     */
    public function testGetAndSetPhonenumbersFunction()
    {
        $phonenumbers = new ArrayCollection(['0123456789', '9876543210']);
        $this->assertSame($phonenumbers, $this->relation->setPhonenumbers($phonenumbers)->getPhonenumbers());
    }

    /**
     * Test addPhonenumber function
     */
    public function testAddPhonenumberFunction()
    {
        // Asserts
        $this->assertSame($this->relation, $this->relation->addPhonenumber('work', '0123456789'));
        $this->assertCount(1, $this->relation->getPhonenumbers());
    }

    /**
     * Test addPhonenumber function with duplicate phonenumber
     */
    public function testAddPhonenumberFunctionWithDuplicatePhonenumber()
    {
        // Add duplicatie phonenumber (work)
        $this->relation->addPhonenumber('work', '0123456789');
        $this->relation->addPhonenumber('work', '9876543210');

        // Asserts
        $this->assertCount(2, $this->relation->getPhonenumbers());
    }

    /**
     * Test removePhonenumber function
     */
    public function testRemovePhonenumberFunction()
    {
        // Add phonenumber
        $this->relation->addPhonenumber('work', '0123456789');

        // Asserts
        //$this->assertSame('0123456789', $this->relation->removePhonenumber('work')); // @todo (INTEGRATED-452)
    }

    /**
     * Test removePhonenumber function with unknown phonenumber
     */
    public function testRemovePhonenumberFunctionWithUnknownPhonenumber()
    {
        // Add phonenumber
        $this->relation->addPhonenumber('work', '0123456789');

        // Asserts
        $this->assertFalse($this->relation->removePhonenumber('private'));
    }
}
