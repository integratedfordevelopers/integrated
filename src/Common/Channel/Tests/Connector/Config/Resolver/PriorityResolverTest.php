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

use ArrayIterator;
use Integrated\Common\Channel\Connector\Config\Resolver\PriorityResolver;
use Integrated\Common\Channel\Connector\Config\ResolverInterface;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Converter\Config\ConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ResolverInterface[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    private $resolvers = [];

    protected function setUp(): void
    {
        $this->resolvers[] = $this->getResolver();
        $this->resolvers[] = $this->getResolver();
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Config\\ResolverInterface', $this->getInstance());
    }

    public function testHasConfig()
    {
        $this->resolvers[0]->expects($this->exactly(2))
            ->method('hasConfig')
            ->withConsecutive([$this->equalTo('config')], [$this->equalTo('this-is-a-config-that-does-not-exist')])
            ->willReturnOnConsecutiveCalls(true, false);

        $this->resolvers[1]->expects($this->exactly(1))
            ->method('hasConfig')
            ->withConsecutive([$this->equalTo('this-is-a-config-that-does-not-exist')])
            ->willReturnOnConsecutiveCalls(false);

        $resolver = $this->getInstance();

        self::assertTrue($resolver->hasConfig('config'));
        self::assertFalse($resolver->hasConfig('this-is-a-config-that-does-not-exist'));
    }

    public function testGetConfig()
    {
        $config = $this->getConfig('config');

        $this->resolvers[0]->expects($this->once())
            ->method('hasConfig')
            ->with($this->equalTo('config'))
            ->willReturn(false);

        $this->resolvers[0]->expects($this->never())
            ->method('getConfig');

        $this->resolvers[1]->expects($this->once())
            ->method('hasConfig')
            ->with($this->equalTo('config'))
            ->willReturn(true);

        $this->resolvers[1]->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('config'))
            ->willReturn($config);

        self::assertSame($config, $this->getInstance()->getConfig('config'));
    }

    public function testGetConfigNotFound()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('this-is-a-config-that-does-not-exist');

        $this->getInstance()->getConfig('this-is-a-config-that-does-not-exist');
    }

    public function testGetConfigs()
    {
        $channel = $this->getChannel();

        $configs = [
            'config1' => $this->getConfig('config1'),
            'config2' => $this->getConfig('config2'),
            'config3' => $this->getConfig('config3'),
            'config4' => $this->getConfig('config4'),
            'config5' => $this->getConfig('config5'),
        ];

        $this->resolvers[0]->expects($this->once())
            ->method('getConfigs')
            ->with($this->identicalTo($channel))
            ->willReturn(new ArrayIterator([$configs['config1'], $configs['config2'], $configs['config3']]));

        $this->resolvers[1]->expects($this->once())
            ->method('getConfigs')
            ->with($this->identicalTo($channel))
            ->willReturn(new ArrayIterator([$this->getConfig('config2'), $this->getConfig('config3'), $configs['config4'], $configs['config5']]));

        $iterator = $this->getInstance()->getConfigs($channel);

        self::assertInstanceOf('Iterator', $iterator);
        self::assertSame($configs, iterator_to_array($iterator));
    }

    /**
     * @return PriorityResolver
     */
    protected function getInstance()
    {
        return new PriorityResolver($this->resolvers);
    }

    /**
     * @return ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver()
    {
        return $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ResolverInterface');
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
     * @return ChannelInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel()
    {
        return $this->createMock('Integrated\\Common\\Channel\\ChannelInterface');
    }
}
