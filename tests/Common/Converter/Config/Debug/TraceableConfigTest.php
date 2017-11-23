<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Converter\Config\Debug;

use Integrated\Common\Converter\Config\ConfigInterface;
use Integrated\Common\Converter\Config\Debug\TraceableConfig;
use Integrated\Tests\Common\Converter\Config\ConfigTest;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class TraceableConfigTest extends ConfigTest
{
    protected $class = 'stdClass';

    public function testInterface()
    {
        $config = $this->getInstance();

        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\ConfigInterface', $config);
        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\Debug\\TraceableConfigInterface', $config);
    }

    public function testGetClass()
    {
        self::assertSame($this->class, $this->getInstance()->getClass());
    }

    /**
     * @param ConfigInterface $parent
     *
     * @return TraceableConfig
     */
    protected function getInstance(ConfigInterface $parent = null)
    {
        return new TraceableConfig($this->class, $this->types, $parent);
    }
}
