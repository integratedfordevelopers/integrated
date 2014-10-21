<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Converter\Config;

use Integrated\Common\Converter\Config\TypeConfig;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TypeConfigTest extends \PHPUnit_Framework_TestCase
{
    private $name = 'name';

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\TypeConfigInterface', $this->getInstance());
    }

    public function testGetName()
    {
        self::assertEquals($this->name, $this->getInstance()->getName());
    }

    public function testHasOptions()
    {
        self::assertTrue($this->getInstance([])->hasOptions());
        self::assertFalse($this->getInstance(null)->hasOptions());
    }

    public function testGetOptions()
    {
        self::assertEquals([], $this->getInstance([])->getOptions());
        self::assertEquals(['test'], $this->getInstance(['test'])->getOptions());
        self::assertNull($this->getInstance(null)->getOptions());
    }

    /**
     * @return TypeConfig
     */
    protected function getInstance(array $options = null)
    {
        return new TypeConfig($this->name, $options);
    }
}
 