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
use Integrated\Common\Channel\Connector\Config\Resolver\MemoryResolverBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolverBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider addConfigProvider
     */
    public function testAddConfig(array $calls, array $expected)
    {
        $builder = $this->getInstance();

        foreach ($calls as $arguments) {
            foreach ($arguments[0] as $config) {
                $builder->addConfig($config, $arguments[1]);
            }
        }

        $resolver = $builder->getResolver();

        foreach ($expected['defaults'] as $config) {
            self::assertSame($config, $resolver->getConfig($config->getName()));
        }
        foreach ($expected['channels'] as $configSet) {
            foreach ($configSet as $config) {
                self::assertSame($config, $resolver->getConfig($config->getName()));
            }
        }
    }

    /**
     * @dataProvider addConfigProvider
     */
    public function testAddConfigs(array $calls, array $expected)
    {
        $builder = $this->getInstance();

        foreach ($calls as $arguments) {
            $builder->addConfigs($arguments[0], $arguments[1]);
        }

        $resolver = $builder->getResolver();

        foreach ($expected['defaults'] as $config) {
            self::assertSame($config, $resolver->getConfig($config->getName()));
        }
        foreach ($expected['channels'] as $configSet) {
            foreach ($configSet as $config) {
                self::assertSame($config, $resolver->getConfig($config->getName()));
            }
        }
    }

    public function addConfigProvider()
    {
        $config1 = $this->getConfig('name1');
        $config2 = $this->getConfig('name2');
        $config3 = $this->getConfig('name3');

        return [
            'with channel string id' => [
                [
                    [[$config1, $config2, $config3], 'channel'],
                ],
                ['channels' => ['channel' => [$config1, $config2, $config3]], 'defaults' => []],
            ],
            'with channel object' => [
                [
                    [[$config1, $config2, $config3], $this->getChannel('channel')],
                ],
                ['channels' => ['channel' => [$config1, $config2, $config3]], 'defaults' => []],
            ],
            'with mixed channels' => [
                [
                    [[$config1], 'channel1'], [[$config2], 'channel2'], [[$config3], $this->getChannel('channel2')],
                ],
                ['channels' => ['channel1' => [$config1], 'channel2' => [$config2, $config3]], 'defaults' => []],
            ],
            'multiple channels' => [
                [
                    [[$config1], 'channel1'], [[$config1, $config2, $config3], 'channel2'], [[$config3], 'channel3'],
                ],
                ['channels' => ['channel1' => [$config1], 'channel2' => [$config1, $config2, $config3], 'channel3' => [$config3]], 'defaults' => []],
            ],
            'defaults' => [
                [
                    [[$config1, $config2, $config3], null],
                ],
                ['channels' => [], 'defaults' => [$config1, $config2, $config3]],
            ],
            'defaults override channel' => [
                [
                    [[$config1, $config2, $config3], 'channel1'], [[$config1], null], [[$config1, $config2, $config3], 'channel2'],
                ],
                ['channels' => ['channel1' => [$config2, $config3], 'channel2' => [$config2, $config3]], 'defaults' => [$config1]],
            ],
        ];
    }

    public function testAddConfigInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);

        $builder = $this->getInstance();
        $builder->addConfig($this->getConfig('name'), 42);
    }

    /**
     * @return MemoryResolverBuilder
     */
    protected function getInstance()
    {
        return new MemoryResolverBuilder();
    }

    /**
     * @param $name
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
