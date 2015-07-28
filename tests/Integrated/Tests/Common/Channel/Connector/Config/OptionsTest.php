<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Channel\Connector\Config;

use Integrated\Common\Channel\Connector\Config\Options;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Config\\OptionsInterface', $this->getInstance());
    }

    public function testConstructor()
    {
        self::assertEquals([], $this->getInstance()->toArray());
    }

    public function testConstructorWithArguments()
    {
        self::assertEquals(['test' => 42, 1 => ['test']], $this->getInstance(['test' => 42, null, ['test']])->toArray());
    }

    public function testSetAndGet()
    {
        $options = $this->getInstance();

        $options->set('key1', 'value 1');
        $options->set('key2', 'value 2');

        self::assertEquals('value 1', $options->get('key1'));
        self::assertEquals('value 2', $options->get('key2'));

        $options->set('key1', 'value 3');
        $options->set('key2', null);

        self::assertEquals('value 3', $options->get('key1'));
        self::assertNull($options->get('key2'));
        self::assertFalse($options->has('key2'));
    }

    public function testSetAndGetArrayAccess()
    {
        $options = $this->getInstance();

        $options['key1'] = 'value 1';
        $options['key2'] = 'value 2';

        self::assertEquals('value 1', $options['key1']);
        self::assertEquals('value 2', $options['key2']);

        $options['key1'] = 'value 3';
        $options['key2'] = null;

        self::assertEquals('value 3', $options['key1']);
        self::assertNull($options['key2']);
        self::assertFalse(isset($options['key2']));
    }

    public function testRemove()
    {
        $options = $this->getInstance();

        $options->set('key', 'value');
        $options->remove('key');

        self::assertNull($options->get('key'));
        self::assertFalse($options->has('key'));
    }

    public function testRemoveArrayAccess()
    {
        $options = $this->getInstance();

        $options['key'] = 'value';
        unset($options['key']);

        self::assertNull($options->get('key'));
        self::assertFalse($options->has('key'));
    }

    public function testHas()
    {
        $options = $this->getInstance();

        self::assertFalse($options->has('key'));

        $options->set('key', 'value');

        self::assertTrue($options->has('key'));
    }

    public function testHasArrayAccess()
    {
        $options = $this->getInstance();

        self::assertFalse(isset($options['key']));

        $options->set('key', 'value');

        self::assertTrue(isset($options['key']));
    }

    public function testClear()
    {
        $options = $this->getInstance();

        $options->set('key1', 'value 1');
        $options->set('key2', 'value 2');

        $options->clear();

        self::assertFalse($options->has('key1'));
        self::assertFalse($options->has('key2'));
    }

    public function testToArray()
    {
        $options = $this->getInstance();

        $options->set('key1', 'value 1');
        $options->set('key2', 'value 2');
        $options->set('key2', null);
        $options->set('key3', ['value 3']);
        $options->set('key4', 42);

        self::assertEquals(['key1' => 'value 1', 'key3' => ['value 3'], 'key4' => 42], $options->toArray());
    }

    public function testCount()
    {
        $options = $this->getInstance();

        $options->set('key1', 'value 1');
        $options->set('key2', 'value 2');
        $options->set('key2', null);
        $options->set('key3', ['value 3']);
        $options->set('key4', 42);

        self::assertCount(3, $options);
    }

    public function testIterator()
    {
        $options = $this->getInstance();

        $options->set('key1', 'value 1');
        $options->set('key2', 'value 2');
        $options->set('key2', null);
        $options->set('key3', ['value 3']);
        $options->set('key4', 42);

        $options = iterator_to_array($options);

        self::assertEquals(['key1' => 'value 1', 'key3' => ['value 3'], 'key4' => 42], $options);
    }

    /**
     * @return Options
     */
    protected function getInstance(array $data = null)
    {
        if ($data) {
            return new Options($data);
        }

        return new Options();
    }
}
