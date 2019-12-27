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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class AuthorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Author
     */
    private $author;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->author = new Author();
    }

    /**
     * Test get- and setType function.
     */
    public function testGetAndSetTypeFunction()
    {
        $type = 'type';
        $this->assertEquals($type, $this->author->setType($type)->getType());
    }

    /**
     * Test get- and setPerson function.
     */
    public function testGetAndSetPersonFunction()
    {
        /* @var $person \Integrated\Bundle\ContentBundle\Document\Content\Relation\Person | \\PHPUnit_Framework_MockObject_MockObject */
        $person = $this->createMock('Integrated\Bundle\ContentBundle\Document\Content\Relation\Person');
        $this->assertSame($person, $this->author->setPerson($person)->getPerson());
    }
}
