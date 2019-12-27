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

use Integrated\Common\Converter\Type\ResolvedType;
use Integrated\Common\Converter\Type\TypeExtensionInterface;
use Integrated\Common\Converter\Type\TypeInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ResolvedTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var TypeInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $type;

    protected function setUp(): void
    {
        $this->type = $this->createMock('Integrated\\Common\\Converter\\Type\\TypeInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\Converter\\Type\\ResolvedTypeInterface', $this->getInstance());
    }

    public function testGetName()
    {
        $this->type->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('test');

        self::assertEquals('test', $this->getInstance()->getName());
    }

    public function testGetType()
    {
        self::assertSame($this->type, $this->getInstance()->getType());
    }

    public function testGetTypeExtensions()
    {
        $extensions = [
            $this->getTypeExtension(),
            $this->getTypeExtension(),
        ];

        self::assertSame($extensions, $this->getInstance($extensions)->getTypeExtensions());
    }

    public function testBuild()
    {
        $container = $this->createMock('Integrated\\Common\\Converter\\ContainerInterface');

        $this->type->expects($this->once())
            ->method('build')
            ->with($this->equalTo($container), $this->equalTo('this-is-the-data'), $this->equalTo(['key' => 'value']));

        $extension1 = $this->getTypeExtension();
        $extension1->expects($this->once())
            ->method('build')
            ->with($this->equalTo($container), $this->equalTo('this-is-the-data'), $this->equalTo(['key' => 'value']));

        $extension2 = $this->getTypeExtension();
        $extension2->expects($this->once())
            ->method('build')
            ->with($this->equalTo($container), $this->equalTo('this-is-the-data'), $this->equalTo(['key' => 'value']));

        $this->getInstance([$extension1, $extension2])->build($container, 'this-is-the-data', ['key' => 'value']);
    }

    public function testBuildOrder()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Type was called first');

        $container = $this->createMock('Integrated\\Common\\Converter\\ContainerInterface');

        $this->type->expects($this->any())
            ->method('build')
            ->willThrowException(new \Exception('Type was called first'));

        $extension = $this->getTypeExtension();
        $extension->expects($this->any())
            ->method('build')
            ->willThrowException(new \Exception('Extension was called first'));

        $this->getInstance([$extension])->build($container, 'this-is-the-data', []);
    }

    /**
     * @param TypeExtensionInterface[] $extensions
     *
     * @return ResolvedType
     */
    protected function getInstance(array $extensions = [])
    {
        return new ResolvedType($this->type, $extensions);
    }

    /**
     * @return TypeExtensionInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTypeExtension()
    {
        return $this->createMock('Integrated\\Common\\Converter\\Type\\TypeExtensionInterface');
    }
}
