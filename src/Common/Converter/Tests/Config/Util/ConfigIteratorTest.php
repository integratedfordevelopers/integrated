<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Config\Util;

use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;
use Integrated\Common\Converter\Config\Util\ConfigIterator;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigIteratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock('Integrated\\Common\\Converter\\Config\\ConfigInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Iterator', $this->getInstance());
    }

    public function testCurrent()
    {
        $types = [
            $this->getType(),
            $this->getType(),
        ];

        $iterator = $this->getInstance($types);

        self::assertSame($types[0], $iterator->current());
        $iterator->next();
        self::assertSame($types[1], $iterator->current());
        $iterator->next();
        self::assertFalse($iterator->current());
    }

    public function testNext()
    {
        $iterator = $this->getInstance();

        // more next calls then items but this should not give a error

        $iterator->next();
        $iterator->next();
        $iterator->next();
        $iterator->next();
    }

    public function testKey()
    {
        $iterator = $this->getInstance([$this->getType(), $this->getType()]);

        self::assertEquals(0, $iterator->key());
        $iterator->next();
        self::assertEquals(1, $iterator->key());
        $iterator->next();
        self::assertNull($iterator->key());
    }

    public function testValid()
    {
        $iterator = $this->getInstance([$this->getType(), $this->getType()]);

        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertFalse($iterator->valid());
    }

    public function testRewind()
    {
        $types = [
            $this->getType(),
            $this->getType(),
        ];

        $iterator = $this->getInstance($types);

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertSame($types[0], $iterator->current());
        self::assertEquals(0, $iterator->key());
    }

    /**
     * @param TypeConfigInterface[] $types
     *
     * @return ConfigIterator
     */
    protected function getInstance(array $types = [])
    {
        $this->config->expects($this->once())
            ->method('getTypes')
            ->willReturn($types);

        return new ConfigIterator($this->config);
    }

    /**
     * @return TypeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface');
    }
}
