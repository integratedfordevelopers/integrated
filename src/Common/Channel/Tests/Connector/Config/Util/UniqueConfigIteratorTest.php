<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Connector\Config\Util;

use ArrayIterator;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Connector\Config\Util\UniqueConfigIterator;
use stdClass;

/**
 * The iterator should only output classes that implement the config interface. So all
 * the instances of stdClass should never show up in any asserts.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UniqueConfigIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Iterator', $this->getInstance());
    }

    public function testCurrent()
    {
        $configs = [
            new stdClass(),
            $this->getConfig('name1'),
            $this->getConfig('name1'),
            $this->getConfig('name2'),
            $this->getConfig('name3'),
            new stdClass(),
        ];

        $iterator = $this->getInstance($configs);
        $iterator->rewind();

        self::assertSame($configs[1], $iterator->current());
        $iterator->next();
        self::assertSame($configs[3], $iterator->current());
        $iterator->next();
        self::assertSame($configs[4], $iterator->current());
        $iterator->next();
        self::assertNull($iterator->current());
    }

    public function testCurrentWithEmptyConfig()
    {
        $iterator = $this->getInstance();
        $iterator->rewind();

        self::assertNull($iterator->current());
    }

    public function testNext()
    {
        $iterator = $this->getInstance();
        $iterator->rewind();

        // more next calls then items but this should not give a error

        $iterator->next();
        $iterator->next();
        $iterator->next();
        $iterator->next();

        self::assertNull($iterator->current());
    }

    public function testKey()
    {
        $configs = [
            new stdClass(),
            $this->getConfig('name1'),
            $this->getConfig('name1'),
            $this->getConfig('name2'),
            new stdClass(),
        ];

        $iterator = $this->getInstance($configs);
        $iterator->rewind();

        self::assertEquals('name1', $iterator->key());
        $iterator->next();
        self::assertEquals('name2', $iterator->key());
        $iterator->next();
        self::assertNull($iterator->key());
    }

    public function testValid()
    {
        $configs = [
            new stdClass(),
            $this->getConfig('name1'),
            $this->getConfig('name1'),
            $this->getConfig('name2'),
            new stdClass(),
        ];

        $iterator = $this->getInstance($configs);
        $iterator->rewind();

        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertFalse($iterator->valid());
    }

    public function testValidWithEmptyConfig()
    {
        $iterator = $this->getInstance();
        $iterator->rewind();

        self::assertFalse($iterator->valid());
    }

    public function testRewind()
    {
        $configs = [
            new stdClass(),
            $this->getConfig('name1'),
            $this->getConfig('name1'),
            $this->getConfig('name2'),
            new stdClass(),
        ];

        $iterator = $this->getInstance($configs);
        $iterator->rewind();

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertSame($configs[1], $iterator->current());
        self::assertEquals('name1', $iterator->key());
    }

    public function testRewindWithEmptyConfig()
    {
        $iterator = $this->getInstance();
        $iterator->rewind();

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertNull($iterator->current());
        self::assertNull($iterator->key());
    }

    /**
     * @param ConfigInterface[] $configs
     *
     * @return UniqueConfigIterator
     */
    protected function getInstance(array $configs = [])
    {
        return new UniqueConfigIterator(new ArrayIterator($configs));
    }

    /**
     * @return ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfig($name)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ConfigInterface');
        $mock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }
}
