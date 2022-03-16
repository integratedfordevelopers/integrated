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
use Integrated\Bundle\ContentBundle\Tests\Document\Content\ContentTest;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
abstract class RelationTest extends ContentTest
{
    /**
     * Relation should extend Relation.
     */
    public function testInstanceOfRelation()
    {
        $this->assertInstanceOf('Integrated\Bundle\ContentBundle\Document\Content\Relation\Relation', $this->getContent());
    }

    /**
     * Test get- and setAccountnumber function.
     */
    public function testGetAndSetAccountnumberFunction()
    {
        $accountnumber = 'accountnumber';
        $this->assertEquals($accountnumber, $this->getContent()->setAccountnumber($accountnumber)->getAccountnumber());
    }

    /**
     * Test get- and setDescription function.
     */
    public function testGetAndSetDescriptionFunction()
    {
        $description = 'description';
        $this->assertEquals($description, $this->getContent()->setDescription($description)->getDescription());
    }

    /**
     * Test get- and setPhonenumbers function.
     */
    public function testGetAndSetPhonenumbersFunction()
    {
        $phonenumbers = new ArrayCollection(['0123456789', '9876543210']);
        $this->assertSame($phonenumbers, $this->getContent()->setPhonenumbers($phonenumbers)->getPhonenumbers());
    }

    /**
     * Test addPhonenumber function.
     */
    public function testAddPhonenumberFunction()
    {
        // Asserts
        $this->assertSame($this->getContent(), $this->getContent()->addPhonenumber('work', '0123456789'));
        $this->assertCount(1, $this->getContent()->getPhonenumbers());
    }

    /**
     * Test addPhonenumber function with duplicate phonenumber.
     */
    public function testAddPhonenumberFunctionWithDuplicatePhonenumber()
    {
        // Add duplicatie phonenumber (work)
        $this->getContent()->addPhonenumber('work', '0123456789');
        $this->getContent()->addPhonenumber('work', '9876543210');

        // Asserts
        $this->assertCount(2, $this->getContent()->getPhonenumbers());
    }

    /**
     * Test removePhonenumber function.
     */
    public function testRemovePhonenumberFunction()
    {
        $this->markTestSkipped('todo INTEGRATED-452');

        // Add phonenumber
        $this->getContent()->addPhonenumber('work', '0123456789');

        // Asserts
        // $this->assertSame('0123456789', $this->getContent()->removePhonenumber('work')); // @todo (INTEGRATED-452)
    }

    /**
     * Test removePhonenumber function with unknown phonenumber.
     */
    public function testRemovePhonenumberFunctionWithUnknownPhonenumber()
    {
        // Add phonenumber
        $this->getContent()->addPhonenumber('work', '0123456789');

        // Asserts
        $this->assertFalse($this->getContent()->removePhonenumber('private'));
    }
}
