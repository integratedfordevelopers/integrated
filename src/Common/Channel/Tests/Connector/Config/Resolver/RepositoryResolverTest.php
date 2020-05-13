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

use Integrated\Common\Channel\Connector\Config\ConfigRepositoryInterface;
use Integrated\Common\Channel\Connector\Config\Resolver\RepositoryResolver;
use Integrated\Common\Content\Channel\ChannelInterface;
use Integrated\Common\Converter\Config\ConfigInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RepositoryResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConfigRepositoryInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock('Integrated\\Common\\Channel\\Connector\\Config\\ConfigRepositoryInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Config\\ResolverInterface', $this->getInstance());
    }

    public function testHasConfig()
    {
        $this->repository->expects($this->exactly(2))
            ->method('find')
            ->withConsecutive([$this->equalTo('config')], [$this->equalTo('this-is-a-config-that-does-not-exist')])
            ->willReturnOnConsecutiveCalls($this->getConfig('config'), null);

        $resolver = $this->getInstance();

        self::assertTrue($resolver->hasConfig('config'));
        self::assertFalse($resolver->hasConfig('this-is-a-config-that-does-not-exist'));
    }

    public function testGetConfig()
    {
        $config = $this->getConfig('config');

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo('config'))
            ->willReturn($config);

        self::assertSame($config, $this->getInstance()->getConfig('config'));
    }

    public function testGetConfigNotFound()
    {
        $this->expectException(\Integrated\Common\Channel\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('this-is-a-config-that-does-not-exist');

        $this->repository->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $this->getInstance()->getConfig('this-is-a-config-that-does-not-exist');
    }

    public function testGetConfigs()
    {
        $channel = $this->getChannel();

        $configs = [
            'config1' => $this->getConfig('config1'),
            'config2' => $this->getConfig('config2'),
            'config3' => $this->getConfig('config3'),
        ];

        $this->repository->expects($this->once())
            ->method('findByChannel')
            ->with($this->identicalTo($channel))
            ->willReturn($configs);

        $iterator = $this->getInstance()->getConfigs($channel);

        self::assertInstanceOf('Iterator', $iterator);
        self::assertSame($configs, iterator_to_array($iterator));
    }

    /**
     * @return RepositoryResolver
     */
    protected function getInstance()
    {
        return new RepositoryResolver($this->repository);
    }

    /**
     * @param string $name
     *
     * @return ConfigInterface | \PHPUnit_Framework_MockObject_MockObject
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
     * @return ChannelInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getChannel()
    {
        return $this->createMock('Integrated\\Common\\Channel\\ChannelInterface');
    }
}
