<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Channel\Tests\Connector\Adapter;

use Integrated\Common\Channel\Connector\Adapter\ManifestInterface;
use Integrated\Common\Channel\Connector\Adapter\RegistryBuilder;
use Integrated\Common\Channel\Connector\AdapterInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Adapter\\RegistryBuilderInterface', $this->getInstance());
    }

    public function testAddAdaptor()
    {
        $adaptor = $this->getAdapter($this->getManifest('test'));

        $builder = $this->getInstance();
        $builder->addAdapter($adaptor);

        self::assertSame(['test' => $adaptor], $builder->getRegistry()->getAdapters());
    }

    public function testAddAdaptors()
    {
        $adaptor1 = $this->getAdapter($this->getManifest('test1'));
        $adaptor2 = $this->getAdapter($this->getManifest('test2'));
        $adaptor3 = $this->getAdapter($this->getManifest('test3'));

        $builder = $this->getInstance();
        $builder->addAdapters([$adaptor1, $adaptor2, $adaptor3]);

        self::assertSame([
            'test1' => $adaptor1,
            'test2' => $adaptor2,
            'test3' => $adaptor3,
        ], $builder->getRegistry()->getAdapters());
    }

    public function testAddAdaptorOverride()
    {
        $adaptor1 = $this->getAdapter($this->getManifest('test'));
        $adaptor2 = $this->getAdapter($this->getManifest('test'));

        $builder = $this->getInstance();

        $builder->addAdapter($adaptor1);
        $builder->addAdapter($adaptor2);

        self::assertSame(['test' => $adaptor2], $builder->getRegistry()->getAdapters());
    }

    /**
     * @return RegistryBuilder
     */
    protected function getInstance()
    {
        return new RegistryBuilder();
    }

    /**
     * @param ManifestInterface $manifest
     *
     * @return AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAdapter(ManifestInterface $manifest)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\Connector\\AdapterInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getManifest')
            ->willReturn($manifest);

        return $mock;
    }

    /**
     * @param $name
     *
     * @return ManifestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getManifest($name)
    {
        $mock = $this->createMock('Integrated\\Common\\Channel\\Connector\\Adapter\\ManifestInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }
}
