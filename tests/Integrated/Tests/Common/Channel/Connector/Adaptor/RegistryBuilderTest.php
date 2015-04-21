<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Channel\Connector\Adaptor;

use Integrated\Common\Channel\Connector\Adaptor\ManifestInterface;
use Integrated\Common\Channel\Connector\Adaptor\RegistryBuilder;
use Integrated\Common\Channel\Connector\AdaptorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Channel\\Connector\\Adaptor\\RegistryBuilderInterface', $this->getInstance());
    }

    public function testAddAdaptor()
    {
        $adaptor = $this->getAdaptor($this->getManifest('test'));

        $builder = $this->getInstance();
        $builder->addAdaptor($adaptor);

        self::assertSame(['test' => $adaptor], $builder->getRegistry()->getAdaptors());
    }

    public function testAddAdaptors()
    {
        $adaptor1 = $this->getAdaptor($this->getManifest('test1'));
        $adaptor2 = $this->getAdaptor($this->getManifest('test2'));
        $adaptor3 = $this->getAdaptor($this->getManifest('test3'));

        $builder = $this->getInstance();
        $builder->addAdaptors([$adaptor1, $adaptor2, $adaptor3]);

        self::assertSame([
            'test1' => $adaptor1,
            'test2' => $adaptor2,
            'test3' => $adaptor3,
        ], $builder->getRegistry()->getAdaptors());
    }

    public function testAddAdaptorOverride()
    {
        $adaptor1 = $this->getAdaptor($this->getManifest('test'));
        $adaptor2 = $this->getAdaptor($this->getManifest('test'));

        $builder = $this->getInstance();

        $builder->addAdaptor($adaptor1);
        $builder->addAdaptor($adaptor2);

        self::assertSame(['test' => $adaptor2], $builder->getRegistry()->getAdaptors());
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
     * @return AdaptorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAdaptor(ManifestInterface $manifest)
    {
        $mock = $this->getMock('Integrated\\Common\\Channel\\Connector\\AdaptorInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getManifest')
            ->willReturn($manifest);

        return $mock;
    }

    /**
     * @param $name
     * @return ManifestInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getManifest($name)
    {
        $mock = $this->getMock('Integrated\\Common\\Channel\\Connector\\Adaptor\\ManifestInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);

        return $mock;
    }
}
