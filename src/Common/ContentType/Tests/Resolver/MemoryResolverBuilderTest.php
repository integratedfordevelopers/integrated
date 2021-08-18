<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\ContentType\Tests\Resolver;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Resolver\MemoryResolverBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolverBuilderTest extends \PHPUnit\Framework\TestCase
{
    public function testAddContentType()
    {
        $type = $this->getType('test');

        $builder = $this->getInstance();
        $builder->addContentType($type);

        self::assertSame($type, $builder->getResolver()->getType('test'));
    }

    public function testAddContentTypes()
    {
        $type1 = $this->getType('test1');
        $type2 = $this->getType('test2');
        $type3 = $this->getType('test1');

        $builder = $this->getInstance();
        $builder->addContentTypes([$type1, $type2, $type3]);

        $resolver = $builder->getResolver();

        self::assertSame($type3, $resolver->getType('test1'));
        self::assertSame($type2, $resolver->getType('test2'));
    }

    public function testGetResolver()
    {
        self::assertInstanceOf('Integrated\\Common\\ContentType\\Resolver\\MemoryResolver', $this->getInstance()->getResolver());
    }

    /**
     * @return MemoryResolverBuilder
     */
    protected function getInstance()
    {
        return new MemoryResolverBuilder();
    }

    /**
     * @param string $name
     *
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType($name)
    {
        $mock = $this->createMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $mock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($name);

        return $mock;
    }
}
