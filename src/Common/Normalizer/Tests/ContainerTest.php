<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Normalizer\Tests;

use Integrated\Common\Normalizer\Container;
use Integrated\Common\Normalizer\ContainerInterface;
use stdClass;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(ContainerInterface::class, $this->getInstance());
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

        $container->add('key4', ['1' => '1', '2' => '2']);
        $container->add('key4', null);
        $container->add('key4', ['3' => '3', '4' => '4']);

        $container->add('key5', true);
        $container->add('key5', false);

        self::assertEquals(['1', '2', '3', null], $container->get('key1'));
        self::assertEquals([4], $container->get('key2'));
        self::assertEquals([5.1, 5.2], $container->get('key3'));
        self::assertEquals([['1' => '1', '2' => '2'], null, ['3' => '3', '4' => '4']], $container->get('key4'));
        self::assertEquals([true, false], $container->get('key5'));
    }

    public function testAddInvalid()
    {
        $this->expectException(\Integrated\Common\Normalizer\Exception\ExceptionInterface::class);

        $container = $this->getInstance();
        $container->add('key', new stdClass());
    }

    public function testSetAndGet()
    {
        $container = $this->getInstance();

        $container->set('key1', 'value 1');
        $container->set('key2', 'value 2');

        self::assertEquals('value 1', $container->get('key1'));
        self::assertEquals('value 2', $container->get('key2'));

        $container->set('key1', 'value 3');
        $container->set('key2', null);

        self::assertEquals('value 3', $container->get('key1'));
        self::assertNull($container->get('key2'));
        self::assertTrue($container->has('key2'));
    }

    public function testSetInvalid()
    {
        $this->expectException(\Integrated\Common\Normalizer\Exception\ExceptionInterface::class);

        $container = $this->getInstance();
        $container->set('key', new stdClass());
    }

    public function testAddAndSetOverwrite()
    {
        $container = $this->getInstance();

        $container->add('key', '1');
        $container->add('key', '2');
        $container->add('key', '3');

        self::assertEquals(['1', '2', '3'], $container->get('key'));

        $container->set('key', '1');

        self::assertEquals('1', $container->get('key'));

        $container->add('key', '2');
        $container->add('key', '3');

        self::assertEquals(['1', '2', '3'], $container->get('key'));
    }

    public function testRemove()
    {
        $container = $this->getInstance();

        $container->add('key', 'value');
        $container->remove('key');

        self::assertNull($container->get('key'));
        self::assertFalse($container->has('key'));

        $container->set('key', 'value');
        $container->remove('key');

        self::assertNull($container->get('key'));
        self::assertFalse($container->has('key'));
    }

    public function testHas()
    {
        $container = $this->getInstance();

        self::assertFalse($container->has('key1'));
        self::assertFalse($container->has('key2'));

        $container->add('key1', 'value');
        $container->set('key2', 'value');

        self::assertTrue($container->has('key1'));
        self::assertTrue($container->has('key2'));
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
        $container->set('key3', '4');
        $container->set('key4', '5');

        self::assertEquals(['key1' => ['1'], 'key2' => ['2', '3'], 'key3' => '4', 'key4' => '5'], $container->toArray());
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

        self::assertEquals([], $container->toArray());

        $container->add('key1', '1');
        $container->add('key2', '2');
        $container->add('key2', '3');
        $container->set('key3', '4');
        $container->set('key4', '5');

        $container = iterator_to_array($container);

        self::assertEquals(['key1' => ['1'], 'key2' => ['2', '3'], 'key3' => '4', 'key4' => '5'], $container);
    }

    /**
     * @return Container
     */
    protected function getInstance()
    {
        return new Container();
    }
}
