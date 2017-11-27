<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Converter\Tests\Type;

use Integrated\Common\Converter\Type\ResolvedTypeFactory;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedTypeFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\ResolvedTypeFactoryInterface', $this->getInstance());
    }

    public function testCreateType()
    {
        $factory = $this->getInstance();

        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\ResolvedType', $factory->createType($this->createMock('Integrated\\Common\\Converter\\Type\\TypeInterface'), []));
    }

    /**
     * @return ResolvedTypeFactory
     */
    protected function getInstance()
    {
        return new ResolvedTypeFactory();
    }
}
