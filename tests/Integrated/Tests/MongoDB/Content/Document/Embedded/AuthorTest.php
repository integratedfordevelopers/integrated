<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\Content\Document\Embedded;

use Integrated\MongoDB\Content\Document\Embedded\Author;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class AuthorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Author
     */
    private $author;

    /**
     * Setup the test
     */
    protected function setUp()
    {
        $this->author = new Author();
    }

    /**
     * Test get- and setType function
     */
    public function testGetAndSetTypeFunction()
    {
        $type = 'type';
        $this->assertEquals($type, $this->author->setType($type)->getType());
    }

    /**
     * Test get- and setPerson function
     */
    public function testGetAndSetPersonFunction()
    {
        /* @var $person \Integrated\MongoDB\Content\Document\Relation\Person | \\PHPUnit_Framework_MockObject_MockObject */
        $person = $this->getMock('Integrated\MongoDB\Content\Document\Relation\Person');
        $this->assertSame($person, $this->author->setPerson($person)->getPerson());
    }
}