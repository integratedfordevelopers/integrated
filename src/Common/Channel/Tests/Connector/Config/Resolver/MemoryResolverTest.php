<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Connector\Config\Resolver;

use Integrated\Common\Channel\ChannelInterface;
use Integrated\Common\Channel\Connector\Config\ConfigInterface;
use Integrated\Common\Channel\Connector\Config\Resolver\MemoryResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolverTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Config\\ResolverInterface', $this->getInstance());
    }

    public function testConstructorWithInvalidChannels()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('this-is-a-config-that-does-not-exist');

        $this->getInstance([], ['this-is-a-config-that-does-not-exist' => null]);
    }

    public function testHasConfig()
    {
        $resolver = $this->getInstance(['config' => $this->getConfig('config')]);

        self::assertTrue($resolver->hasConfig('config'));
        self::assertFalse($resolver->hasConfig('this-is-a-config-that-does-not-exist'));
    }

    public function testGetConfig()
    {
        $config = $this->getConfig('config');

        self::assertSame($config, $this->getInstance(['config' => $config])->getConfig('config'));
    }

    public function testGetConfigNotFound()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('this-is-a-config-that-does-not-exist');

        $this->getInstance()->getConfig('this-is-a-config-that-does-not-exist');
    }

    public function testGetConfigs()
    {
        $configs = [
            'config1' => $this->getConfig('config1'),
            'config2' => $this->getConfig('config2'),
            'config3' => $this->getConfig('config3'),
            'config4' => $this->getConfig('config4'),
            'config5' => $this->getConfig('config5'),
        ];

        // Config5 is not connected to any channel or set as default for every channel. This is
        // something that is not possible if the builder is used, as the channel is required and
        // if not given it will be added as a channel default.

        $channels = [
            'config1' => ['channel1'],
            'config2' => ['channel2'],
            'config3' => ['channel1', 'channel2'],
            'config4' => null,
        ];

        $resolver = $this->getInstance($configs, $channels);

        $iterator = $resolver->getConfigs($this->getChannel('channel1'));

        self::assertInstanceOf('Iterator', $iterator);
        self::assertSame(['config4' => $configs['config4'], 'config1' => $configs['config1'], 'config3' => $configs['config3']], iterator_to_array($iterator));

        $iterator = $resolver->getConfigs($this->getChannel('channel2'));

        self::assertInstanceOf('Iterator', $iterator);
        self::assertSame(['config4' => $configs['config4'], 'config2' => $configs['config2'], 'config3' => $configs['config3']], iterator_to_array($iterator));

        $iterator = $resolver->getConfigs($this->getChannel('channel3'));

        self::assertInstanceOf('Iterator', $iterator);
        self::assertSame(['config4' => $configs['config4']], iterator_to_array($iterator));
    }

    /**
     * @param ConfigInterface[] $configs
     * @param array             $channels
     *
     * @return MemoryResolver
     */
    protected function getInstance(array $configs = [], array $channels = [])
    {
        return new MemoryResolver($configs, $channels);
    }

    /**
     * @param string $name
     *
     * @return ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConfig($name)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ConfigInterface');
        $mock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }

    /**
     * @param string $id
     *
     * @return ChannelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel($id)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\ChannelInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($id);

        return $mock;
    }
}
