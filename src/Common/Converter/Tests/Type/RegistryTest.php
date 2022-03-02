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

use Integrated\Common\Converter\Type\Registry;
use Integrated\Common\Converter\Type\ResolvedTypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class RegistryTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\RegistryInterface', $this->getInstance());
    }

    public function testHasType()
    {
        $registry = $this->getInstance([
            'test1' => $this->getType(),
            'test2' => $this->getType(),
        ]);

        self::assertTrue($registry->hasType('test1'));
        self::assertTrue($registry->hasType('test2'));
        self::assertFalse($registry->hasType('test3'));
    }

    public function testHasTypeInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        $this->getInstance()->hasType(42);
    }

    public function testGetType()
    {
        $type = $this->getType();

        self::assertSame($type, $this->getInstance(['test' => $type])->getType('test'));
    }

    public function testGetTypeInvalidArgument()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);

        $this->getInstance()->getType(42);
    }

    public function testGetTypeNotFound()
    {
        $this->expectException(\Integrated\Common\Converter\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('this-is-a-type-that-does-not-exist');

        $this->getInstance()->getType('this-is-a-type-that-does-not-exist');
    }

    /**
     * @param ResolvedTypeInterface[] $types
     *
     * @return Registry
     */
    protected function getInstance(array $types = [])
    {
        return new Registry($types);
    }

    /**
     * @return ResolvedTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Type\\ResolvedTypeInterface');
    }
}
