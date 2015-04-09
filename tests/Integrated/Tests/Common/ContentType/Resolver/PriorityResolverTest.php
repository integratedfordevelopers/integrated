<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Common\ContentType\Resolver;

use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\Iterator;
use Integrated\Common\ContentType\Resolver\PriorityResolver;
use Integrated\Common\ContentType\ResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PriorityResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeInterface[] | \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $types = [];

    /**
     * @var ResolverInterface[] | \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $resolvers = [];

    protected function setUp()
    {
        $this->types[] = $this->getType('type 1');
        $this->types[] = $this->getType('type 2');
        $this->types[] = $this->getType('type 2');

        $this->resolvers[] = $this->getResolver($this->types[0]);
        $this->resolvers[] = $this->getResolver($this->types[1]);
        $this->resolvers[] = $this->getResolver($this->types[2]);
    }

    public function testInterface()
   	{
   		self::assertInstanceOf('Integrated\\Common\\ContentType\\ResolverInterface', $this->getInstance());
   	}

    public function testHasResolver()
    {
        $resolver = $this->getInstance();

        self::assertTrue($resolver->hasResolver($this->resolvers[2]));
        self::assertTrue($resolver->hasResolver($this->resolvers[1]));
        self::assertTrue($resolver->hasResolver($this->resolvers[0]));

        self::assertFalse($resolver->hasResolver($this->getResolver($this->getType('type 3'))));
    }

    public function testGetResolvers()
    {
        self::assertSame($this->resolvers, $this->getInstance()->getResolvers());
    }

    public function testGetType()
    {
        $resolver = $this->getInstance();

        self::assertSame($this->types[1], $resolver->getType('type 2'));
        self::assertSame($this->types[0], $resolver->getType('type 1'));
    }

    /**
     * @expectedException \Integrated\Common\ContentType\Exception\ExceptionInterface
     * @expectedExceptionMessage "type 3"
     */
    public function testGetTypeNotFound()
    {
        $this->getInstance()->getType('type 3');
    }

    public function testHasType()
    {
        $resolver = $this->getInstance();

        self::assertFalse($resolver->hasType('type 3'));
        self::assertTrue($resolver->hasType('type 2'));
        self::assertTrue($resolver->hasType('type 1'));
    }

    public function testGetTypes()
    {
        $iterator = $this->getInstance()->getTypes();

        self::assertInstanceOf('Integrated\\Common\\ContentType\\IteratorInterface', $iterator);
        self::assertSame(['type 1' => $this->types[0], 'type 2' => $this->types[1]], iterator_to_array($iterator));
    }

    /**
     * @return PriorityResolver
     */
    protected function getInstance()
    {
        return new PriorityResolver($this->resolvers);
    }

    /**
     * @param ContentTypeInterface $type
     *
     * @return ResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getResolver(ContentTypeInterface $type)
    {
        $mock = $this->getMock('Integrated\\Common\\ContentType\\ResolverInterface');

        $mock->expects($this->any())
            ->method('hasType')
            ->willReturnCallback(function ($arg) use ($type) {
                return (bool) ($arg == $type->getType());
            });

        $mock->expects($this->any())
            ->method('getType')
            ->willReturnCallback(function ($arg) use ($type) {
                if ($arg == $type->getType()) {
                    return $type;
                }

                throw new \Exception('ERROR ERROR');
            });

        $mock->expects($this->any())
            ->method('getTypes')
            ->willReturn(new Iterator([$type->getType() => $type]));

        return $mock;
    }

    /**
     * @param string $name
     *
     * @return ContentTypeInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType($name)
    {
        $mock = $this->getMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
        $mock->expects($this->any())
            ->method('getType')
            ->willReturn($name);

        return $mock;
    }
}
