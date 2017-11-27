<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests;

use Integrated\Common\Converter\Container;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\ContainerInterface', $this->getInstance());
    }

    public function testAddAndGet()
    {
        $container = $this->getInstance();

        $container->add('key1', '1');
        $container->add('key1', '2');
        $container->add('key1', '3');
        $container->add('key1', null);

        $container->add('key2', 4);

        $container->add('key3', 5.1);
        $container->add('key3', 5.2);

        self::assertEquals(['1', '2', '3'], $container->get('key1'));
        self::assertEquals([4], $container->get('key2'));
        self::assertEquals([5.1, 5.2], $container->get('key3'));
    }

    public function testAddInvalid()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        $container = $this->getInstance();
        $container->add('key', []);
    }

    public function testSetAndGet()
    {
        $container = $this->getInstance();

        $container->set('key1', 'value 1');
        $container->set('key2', 'value 2');

        self::assertEquals(['value 1'], $container->get('key1'));
        self::assertEquals(['value 2'], $container->get('key2'));

        $container->set('key1', 'value 3');
        $container->set('key2', null);

        self::assertEquals(['value 3'], $container->get('key1'));
        self::assertNull($container->get('key2'));
        self::assertFalse($container->has('key2'));
    }

    public function testSetInvalid()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        $container = $this->getInstance();
        $container->set('key', []);
    }

    public function testRemove()
    {
        $container = $this->getInstance();

        $container->set('key', 'value');
        $container->remove('key');

        self::assertNull($container->get('key'));
        self::assertFalse($container->has('key'));
    }

    public function testHas()
    {
        $container = $this->getInstance();

        self::assertFalse($container->has('key'));

        $container->set('key', 'value');

        self::assertTrue($container->has('key'));
    }

    public function testClear()
    {
        $container = $this->getInstance();

        $container->set('key1', 'value 1');
        $container->set('key2', 'value 2');

        $container->clear();

        self::assertFalse($container->has('key1'));
        self::assertFalse($container->has('key2'));
    }

    public function testToArray()
    {
        $container = $this->getInstance();

        self::assertEquals([], $container->toArray());

        $container->add('key1', '1');
        $container->add('key2', '2');
        $container->add('key2', '3');

        self::assertEquals(['key1' => ['1'], 'key2' => ['2', '3']], $container->toArray());
    }

    public function testCount()
    {
        $container = $this->getInstance();

        self::assertCount(0, $container);

        $container->add('key1', '1');
        $container->add('key2', '2');
        $container->add('key2', '3');

        self::assertCount(2, $container);
    }

    public function testIterator()
    {
        $container = $this->getInstance();

        $container->add('key1', '1');
        $container->add('key2', '2');
        $container->add('key2', '3');

        $container = iterator_to_array($container);

        self::assertEquals(['key1' => ['1'], 'key2' => ['2', '3']], $container);
    }

    /**
     * @return Container
     */
    protected function getInstance()
    {
        return new Container();
    }
}
