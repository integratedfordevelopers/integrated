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
use Integrated\Common\ContentType\Resolver\MemoryResolver;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MemoryResolverTest extends \PHPUnit\Framework\TestCase
{
    public function testInterface()
    {
        self::assertInstanceOf('Integrated\\Common\\ContentType\\ResolverInterface', $this->getInstance());
    }

    public function testGetType()
    {
        $type = $this->getType();

        self::assertSame($type, $this->getInstance(['found' => $type])->getType('found'));
    }

    public function testGetTypeNoString()
    {
        $this->expectException(\Integrated\Common\ContentType\Exception\ExceptionInterface::class);

        $this->getInstance()->getType(['not a string']);
    }

    public function testGetTypeNotFound()
    {
        $this->expectException(\Integrated\Common\ContentType\Exception\ExceptionInterface::class);
        $this->expectExceptionMessage('"not found"');

        $this->getInstance()->getType('not found');
    }

    public function testHasType()
    {
        $instance = $this->getInstance(['found' => $this->getType()]);

        self::assertTrue($instance->hasType('found'));
        self::assertFalse($instance->hasType('not found'));
    }

    public function testHasTypeNoString()
    {
        $this->expectException(\Integrated\Common\ContentType\Exception\ExceptionInterface::class);

        $this->getInstance()->hasType(['not a string']);
    }

    public function testGetTypes()
    {
        $types = [
            'type 1' => $this->getType(),
            'type 2' => $this->getType(),
        ];

        $iterator = $this->getInstance($types)->getTypes();

        self::assertInstanceOf('Integrated\\Common\\ContentType\\IteratorInterface', $iterator);
        self::assertSame($types, iterator_to_array($iterator));
    }

    /**
     * @param ContentTypeInterface[] $types
     *
     * @return MemoryResolver
     */
    protected function getInstance(array $types = [])
    {
        return new MemoryResolver($types);
    }

    /**
     * @return ContentTypeInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getType()
    {
        return $this->createMock('Integrated\\Common\\ContentType\\ContentTypeInterface');
    }
}
