<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Converter;

use Integrated\Common\Converter\FilterContainerFactory;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FilterContainerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\ContainerFactoryInterface', $this->getInstance());
    }

    public function testCreateContainer()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\FilterContainer', $this->getInstance()->createContainer());
    }

    /**
     * @return FilterContainerFactory
     */
    protected function getInstance()
    {
        return new FilterContainerFactory();
    }
}
