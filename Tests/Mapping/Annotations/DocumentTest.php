<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\Tests\Mapping\Annotations;

use Integrated\Bundle\SolrBundle\Mapping\Annotations\Document;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor with valid data
     */
    public function testConstructorWithValidData()
    {
        $document = new Document(array('index' => 1));
        $this->assertEquals(1, $document->getIndex());
    }

    /**
     * Test the constructor with invalid data
     *
     * @expectedException \BadMethodCallException
     */
    public function testConstructorWithInvalidData()
    {
        new Document(array('henk' => 'de vries'));
    }

    /**
     * Test get- and setIndex function
     */
    public function testGetAndSetIndexFunction()
    {
        $index = 1;
        $document = new Document(array());

        $this->assertSame($document, $document->setIndex($index));
        $this->assertSame($index, $document->getIndex());
    }
}