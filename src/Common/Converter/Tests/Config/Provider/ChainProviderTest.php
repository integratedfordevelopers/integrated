<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Config\Provider;

use Integrated\Common\Converter\Config\Provider\ChainProvider;
use Integrated\Common\Converter\Config\TypeProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChainProviderTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Config\\TypeProviderInterface', $this->getInstance());
    }

    public function testAddProvider()
    {
        $providers = [
            $this->getProvider(),
            $this->getProvider(),
        ];

        $provider = $this->getInstance();

        $provider->addProvider($providers[0]);
        $provider->addProvider($providers[1]);

        self::assertSame($providers, $provider->getProviders());
    }

    public function testHasProvider()
    {
        $providers = [
            $this->getProvider(),
            $this->getProvider(),
        ];

        $provider = $this->getInstance();

        $provider->addProvider($providers[0]);
        $provider->addProvider($providers[1]);

        self::assertTrue($provider->hasProvider($providers[0]));
        self::assertTrue($provider->hasProvider($providers[1]));
        self::assertFalse($provider->hasProvider($this->getProvider()));
    }

    public function testRemovedProvider()
    {
        $providers = [
            $this->getProvider(),
            $this->getProvider(),
        ];

        $provider = $this->getInstance();

        $provider->addProvider($providers[0]);
        $provider->addProvider($providers[1]);

        $provider->removeProvider($providers[0]);

        self::assertSame([$providers[1]], $provider->getProviders());

        $provider->removeProvider($providers[1]);

        self::assertEquals([], $provider->getProviders());
    }

    public function testClearProviders()
    {
        $provider = $this->getInstance();

        $provider->addProvider($this->getProvider());
        $provider->addProvider($this->getProvider());
        $provider->clearProviders();

        self::assertEquals([], $provider->getProviders());
    }

    public function testNoProviders()
    {
        $provider = $this->getInstance();

        self::assertEquals([], $provider->getProviders());
        self::assertEquals([], $provider->getTypes('class'));
    }

    public function testGetTypes()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject[] $providers */
        $providers = [
            $this->getProvider(),
            $this->getProvider(),
            $this->getProvider(),
        ];

        $provider = $this->getInstance();

        $provider->addProvider($providers[0]);
        $provider->addProvider($providers[1]);
        $provider->addProvider($providers[2]);

        $types = [
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface'),
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface'),
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface'),
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface'),
            $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface'),
        ];

        $providers[0]->expects($this->once())
            ->method('getTypes')
            ->with($this->equalTo('class'))
            ->willReturn([$types[0], $types[1], $types[2]]);

        $providers[1]->expects($this->once())
            ->method('getTypes')
            ->with($this->equalTo('class'))
            ->willReturn([]);

        $providers[2]->expects($this->once())
            ->method('getTypes')
            ->with($this->equalTo('class'))
                ->willReturn([$types[3], $types[4]]);

        self::assertSame($types, $provider->getTypes('class'));
    }

    /**
     * @return ChainProvider
     */
    protected function getInstance()
    {
        return new ChainProvider();
    }

    /**
     * @return TypeProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getProvider()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Config\\TypeProviderInterface');
    }
}
