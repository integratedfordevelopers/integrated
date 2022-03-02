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
use Integrated\Common\ContentType\Iterator;
use Integrated\Common\ContentType\Resolver\PriorityIterator;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @covers \Integrated\Common\ContentType\Resolver\PriorityIterator
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf('Integrated\\Common\\ContentType\\IteratorInterface', $this->getInstance());
    }

    public function testCurrent()
    {
        $types = [
            $this->getType('type 1'),
            $this->getType('type 2'),
            $this->getType('type 3'),
        ];

        $iterator = $this->getInstance([
            $this->getResolver(['type 1' => $types[0], 'type 2' => $types[1]]),
            $this->getResolver(),
            $this->getResolver(['type 3' => $types[2], 'type 1' => $types[1]]),
        ]);

        self::assertSame($types[0], $iterator->current());
        $iterator->next();
        self::assertSame($types[1], $iterator->current());
        $iterator->next();
        self::assertSame($types[2], $iterator->current());
        $iterator->next();
        self::assertNull($iterator->current());
    }

    public function testCurrentWithOutResolvers()
    {
        self::assertNull($this->getInstance()->current());
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
        $iterator = $this->getInstance([
            $this->getResolver(['type 1' => $this->getType('type 1'), 'type 2' => $this->getType('type 2')]),
        ]);

        self::assertEquals('type 1', $iterator->key());
        $iterator->next();
        self::assertEquals('type 2', $iterator->key());
        $iterator->next();
        self::assertNull($iterator->key());
    }

    public function testValid()
    {
        $iterator = $this->getInstance([
            $this->getResolver(['type 1' => $this->getType('type 1'), 'type 2' => $this->getType('type 2')]),
        ]);

        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertFalse($iterator->valid());
    }

    public function testValidWithOutResolvers()
    {
        self::assertFalse($this->getInstance()->valid());
    }

    public function testRewind()
    {
        $types = [
            $this->getType('type 1'),
            $this->getType('type 2'),
        ];

        $iterator = $this->getInstance([
            $this->getResolver(['type 1' => $types[0]]),
            $this->getResolver(),
            $this->getResolver(['type 2' => $types[1]]),
        ]);

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertSame($types[0], $iterator->current());
        self::assertEquals('type 1', $iterator->key());
    }

    /**
     * @param ResolverInterface[] $resolvers
     *
     * @return PriorityIterator
     */
    protected function getInstance(array $resolvers = [])
    {
        return new PriorityIterator($resolvers);
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

    /**
     * @param ContentTypeInterface[] $types
     *
     * @return ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver(array $types = [])
    {
        $mock = $this->createMock('Integrated\\Common\\ContentType\\ResolverInterface');
        $mock->expects($this->any())
            ->method('getTypes')
            ->willReturn(new Iterator($types));

        return $mock;
    }
}
