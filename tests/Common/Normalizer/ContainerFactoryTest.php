<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\Normalizer;

use Integrated\Common\Normalizer\Container;
use Integrated\Common\Normalizer\ContainerFactory;
use Integrated\Common\Normalizer\ContainerFactoryInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContainerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf(ContainerFactoryInterface::class, $this->getInstance());
    }

    public function testCreateContainer()
    {
        self::assertInstanceOf(Container::class, $this->getInstance()->createContainer());
    }

    /**
     * @return ContainerFactory
     */
    protected function getInstance()
    {
        return new ContainerFactory();
    }
}
