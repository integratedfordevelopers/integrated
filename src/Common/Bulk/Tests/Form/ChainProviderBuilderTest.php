<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Bulk\Tests\Form;

use Integrated\Common\Bulk\Form\ChainProvider;
use Integrated\Common\Bulk\Form\ChainProviderBuilder;
use Integrated\Common\Bulk\Form\ConfigProviderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChainProviderBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddProvider()
    {
        $provider1 = $this->getProvider();
        $provider2 = $this->getProvider();

        $builder = $this->getInstance();

        $builder->addProvider($provider1);
        $builder->addProvider($provider2);
        $builder->addProvider($provider1);

        $provider = $builder->getProvider();

        $reflection = new \ReflectionProperty(ChainProvider::class, 'providers');
        $reflection->setAccessible(true);

        self::assertEquals([$provider1, $provider2], $reflection->getValue($provider));
    }

    public function testGetProvider()
    {
        self::assertInstanceOf(ChainProvider::class, $this->getInstance()->getProvider());
    }

    /**
     * @return ChainProviderBuilder
     */
    protected function getInstance()
    {
        return new ChainProviderBuilder();
    }

    /**
     * @return ConfigProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getProvider()
    {
        return $this->createMock(ConfigProviderInterface::class);
    }
}
