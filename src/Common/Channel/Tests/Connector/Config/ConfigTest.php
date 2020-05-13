<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Connector\Config;

use Integrated\Common\Channel\Connector\Config\Config;
use Integrated\Common\Channel\Connector\Config\OptionsInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OptionsInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $options;

    protected function setUp(): void
    {
        $this->options = $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\OptionsInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Config\\ConfigInterface', $this->getInstance());
    }

    public function testGetName()
    {
        self::assertEquals('name', $this->getInstance()->getName());
    }

    public function testGetAdaptor()
    {
        self::assertEquals('adapter', $this->getInstance()->getAdapter());
    }

    public function testGetOptions()
    {
        self::assertSame($this->options, $this->getInstance()->getOptions());
    }

    public function testGetPublicationStartDate()
    {
        self::assertNull($this->getInstance()->getPublicationStartDate());
    }

    protected function getInstance()
    {
        return new Config('name', 'adapter', $this->options, null);
    }
}
