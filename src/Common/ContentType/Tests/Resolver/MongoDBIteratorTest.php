<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Tests\Resolver;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Resolver\MongoDBIterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\\Common\\ContentType\\IteratorInterface', $this->getInstance());
    }

    public function testCurrent()
    {
        $types = [
            $this->getType('test1'),
            $this->getType('test2'),
        ];

        $iterator = $this->getInstance($types);

        self::assertSame($types[0], $iterator->current());
        $iterator->next();
        self::assertSame($types[1], $iterator->current());
        $iterator->next();
        self::assertNull($iterator->current());
    }

    public function testNext()
    {
        $iterator = $this->getInstance();

        // more next calls then items but this should not give a error

        $iterator->next();
        $iterator->next();
        $iterator->next();
        $iterator->next();

        self::assertNull($iterator->current());
    }

    public function testKey()
    {
        $iterator = $this->getInstance([$this->getType('test1'), $this->getType('test2')]);

        self::assertEquals('test1', $iterator->key());
        $iterator->next();
        self::assertEquals('test2', $iterator->key());
        $iterator->next();
        self::assertNull($iterator->key());
    }

    public function testValid()
    {
        $iterator = $this->getInstance([$this->getType('test1'), $this->getType('test2')]);

        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertFalse($iterator->valid());
    }

    public function testRewind()
    {
        $types = [
            $this->getType('test1'),
            $this->getType('test2'),
        ];

        $iterator = $this->getInstance($types);

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertSame($types[0], $iterator->current());
        self::assertEquals('test1', $iterator->key());
    }

    /**
     * @param ContentTypeInterface[] $types
     *
     * @return MongoDBIterator
     */
    protected function getInstance(array $types = [])
    {
        return new MongoDBIterator($types);
    }

    /**
     * @param string $name
     *
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType($name)
    {
        $mock = $this->createMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $mock->expects($this->any())
            ->method('getId')
            ->willReturn($name);

        return $mock;
    }
}
