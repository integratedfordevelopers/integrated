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
use Integrated\Common\Converter\Config\Util\ParentAwareConfigIterator;

/**
 * Test the ParentAwareConfigIterator.
 *
 * Note on test setup:
 * Like you can see all the test (that are not with empty config) the root parent has not types
 * and so does the top child. The test are written like this because the iterator should be able to
 * handle this with out problem even if this situation would probably not happen in production.
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ParentAwareConfigIteratorTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Iterator', $this->getInstance());
    }

    public function testCurrent()
    {
        $types = [
            $this->getType(),
            $this->getType(),
            $this->getType(),
        ];

        $config = $this->getConfig();
        $config = $this->getConfig([$types[0], $types[1]], $config);
        $config = $this->getConfig([$types[2]], $config);
        $config = $this->getConfig([], $config);

        $iterator = $this->getInstance($config);

        self::assertSame($types[0], $iterator->current());
        $iterator->next();
        self::assertSame($types[1], $iterator->current());
        $iterator->next();
        self::assertSame($types[2], $iterator->current());
        $iterator->next();
        self::assertFalse($iterator->current());
    }

    public function testCurrentWithEmptyConfig()
    {
        self::assertFalse($this->getInstance()->current());
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
        $config = $this->getConfig();
        $config = $this->getConfig([$this->getType(), $this->getType()], $config);
        $config = $this->getConfig([], $config);

        $iterator = $this->getInstance($config);

        self::assertEquals(0, $iterator->key());
        $iterator->next();
        self::assertEquals(1, $iterator->key());
        $iterator->next();
        self::assertNull($iterator->key());
    }

    public function testValid()
    {
        $config = $this->getConfig();
        $config = $this->getConfig([$this->getType(), $this->getType()], $config);
        $config = $this->getConfig([], $config);

        $iterator = $this->getInstance($config);

        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertTrue($iterator->valid());
        $iterator->next();
        self::assertFalse($iterator->valid());
    }

    public function testValidWithEmptyConfig()
    {
        self::assertFalse($this->getInstance()->valid());
    }

    public function testRewind()
    {
        $types = [
            $this->getType(),
            $this->getType(),
        ];

        $config = $this->getConfig();
        $config = $this->getConfig($types, $config);
        $config = $this->getConfig([], $config);

        $iterator = $this->getInstance($config);

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertSame($types[0], $iterator->current());
        self::assertEquals(0, $iterator->key());
    }

    public function testRewindWithEmptyConfig()
    {
        $iterator = $this->getInstance();

        $iterator->next();
        $iterator->next();

        $iterator->rewind();

        self::assertFalse($iterator->current());
        self::assertNull($iterator->key());
    }

    /**
     * @param ConfigInterface $config
     *
     * @return ParentAwareConfigIterator
     */
    protected function getInstance(ConfigInterface $config = null)
    {
        if ($config === null) {
            $config = $this->getConfig([], $this->getConfig());
        }

        return new ParentAwareConfigIterator($config);
    }

    /**
     * @return TypeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface');
    }

    /**
     * @param TypeConfigInterface[] $types
     * @param ConfigInterface       $parent
     *
     * @return ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfig(array $types = [], ConfigInterface $parent = null)
    {
        $mock = $this->createMock('Integrated\\Common\\Converter\\Config\\ConfigInterface');
        $mock->expects($this->once())
            ->method('getParent')
            ->willReturn($parent);

        $mock->expects($this->once())
            ->method('getTypes')
            ->willReturn($types);

        return $mock;
    }
}
