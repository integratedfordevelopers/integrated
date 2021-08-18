<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Config;

use Integrated\Common\Converter\Config\Config;
use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\TypeConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TypeConfigInterface[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $types = [];

    protected function setUp(): void
    {
        $this->types = [
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface'),
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeConfigInterface'),
        ];
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\ConfigInterface', $this->getInstance());
    }

    public function testGetTypes()
    {
        self::assertSame($this->types, $this->getInstance()->getTypes());
    }

    public function testHasParent()
    {
        self::assertTrue($this->getInstance($this->getConfig())->hasParent());
        self::assertFalse($this->getInstance()->hasParent());
    }

    public function testGetParent()
    {
        $parent = $this->getConfig();

        self::assertSame($parent, $this->getInstance($parent)->getParent());
        self::assertNull($this->getInstance()->getParent());
    }

    /**
     * @param ConfigInterface $parent
     *
     * @return Config
     */
    protected function getInstance(ConfigInterface $parent = null)
    {
        return new Config($this->types, $parent);
    }

    /**
     * @return ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfig()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Config\\ConfigInterface');
    }
}
