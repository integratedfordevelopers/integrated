<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Solr;

use Integrated\Tests\Common\Solr\Fixtures\Configurable;

/**
 * @covers \Integrated\Common\Solr\Configurable
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigurableTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        self::assertEquals(['key1' => 'default1', 'key2' => 'default2'], $this->getInstance()->getOptions());
        self::assertEquals(['key1' => 'value1', 'key2' => 'value2'], $this->getInstance(['key1' => 'value1', 'key2' => 'value2'])->getOptions());
    }

    public function testSetGetOptions()
    {
        $instance = $this->getInstance();
        $instance->setOptions(['key1' => 'value1', 'key2' => 'value2']);

        self::assertEquals(['key1' => 'value1', 'key2' => 'value2'], $instance->getOptions());

        $instance->setOptions(['key1' => 'value1']);

        self::assertEquals(['key1' => 'value1', 'key2' => 'default2'], $instance->getOptions());

        $instance->setOptions();

        self::assertEquals(['key1' => 'default1', 'key2' => 'default2'], $instance->getOptions());
    }

    public function testSetGetOption()
    {
        $instance = $this->getInstance();
        $instance->setOption('key1', 'value1');

        self::assertEquals('value1', $instance->getOption('key1'));
        self::assertEquals(['key1' => 'value1', 'key2' => 'default2'], $instance->getOptions());

        $instance->setOption('key2', 'value2');

        self::assertEquals('value2', $instance->getOption('key2'));
        self::assertEquals(['key1' => 'value1', 'key2' => 'value2'], $instance->getOptions());

        self::assertNull($instance->getOption('does-not-exist'));
    }

    public function testHasOption()
    {
        $instance = $this->getInstance();

        self::assertTrue($instance->hasOption('key1'));
        self::assertFalse($instance->hasOption('does-not-exist'));

        $instance->setOption('key1', null);

        self::assertFalse($instance->hasOption('key1'));
    }

    /**
     * @return Configurable
     */
    public function getInstance(array $options = [])
    {
        return new Configurable($options);
    }
}
