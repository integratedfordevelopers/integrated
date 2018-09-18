<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig\Manager;

use ArrayIterator;
use Integrated\Bundle\ContentBundle\FormConfig\Manager\DefaultAwareIterator;
use Integrated\Bundle\ContentBundle\FormConfig\Manager\DefaultAwareManager;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\NotFoundException;
use Integrated\Common\FormConfig\FormConfigEditableInterface;
use Integrated\Common\FormConfig\FormConfigFactoryInterface;
use Integrated\Common\FormConfig\FormConfigInterface;
use Integrated\Common\FormConfig\FormConfigManagerInterface;

class DefaultAwareManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormConfigManagerInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $manager;

    /**
     * @var FormConfigFactoryInterface | \PHPUnit\Framework\MockObject\MockObject
     */
    private $factory;

    protected function setUp()
    {
        $this->manager = $this->createMock(FormConfigManagerInterface::class);
        $this->factory = $this->createMock(FormConfigFactoryInterface::class);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigManagerInterface::class, new DefaultAwareManager($this->manager, $this->factory));
    }

    public function testGet()
    {
        $type = $this->createMock(ContentTypeInterface::class);

        $config = [
            $this->createMock(FormConfigInterface::class),
            $this->createMock(FormConfigInterface::class),
        ];

        $this->manager->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [$this->identicalTo($type), $this->equalTo('key1')],
                [$this->identicalTo($type), $this->equalTo('key2')]
            )
            ->willReturnOnConsecutiveCalls($config[0], $config[1]);

        $this->factory->expects($this->never())
            ->method($this->anything());

        $manager = new DefaultAwareManager($this->manager, $this->factory);

        $this->assertSame($config[0], $manager->get($type, 'key1'));
        $this->assertSame($config[1], $manager->get($type, 'key2'));
    }

    public function testGetNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->manager->expects($this->once())
            ->method('get')
            ->willThrowException(new NotFoundException('content_type', 'key'));

        $this->factory->expects($this->never())
            ->method($this->anything());

        (new DefaultAwareManager($this->manager, $this->factory))->get(
            $this->createMock(ContentTypeInterface::class),
            'key'
        );
    }

    public function testGetCreateDefault()
    {
        $this->manager->expects($this->once())
            ->method('get')
            ->willThrowException(new NotFoundException('content_type', 'default'));

        $type = $this->createMock(ContentTypeInterface::class);

        $this->factory->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($type), $this->equalTo('default'))
            ->willReturn($config = $this->createMock(FormConfigEditableInterface::class));

        $this->assertSame($config, (new DefaultAwareManager($this->manager, $this->factory))->get($type, 'default'));
    }

    public function testHas()
    {
        $type = $this->createMock(ContentTypeInterface::class);

        $this->manager->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(
                [$this->identicalTo($type), $this->equalTo('key1')],
                [$this->identicalTo($type), $this->equalTo('key2')]
            )
            ->willReturnOnConsecutiveCalls(true, false);

        $manager = new DefaultAwareManager($this->manager, $this->factory);

        $this->assertTrue($manager->has($type, 'key1'));
        $this->assertFalse($manager->has($type, 'key2'));
    }

    public function testHasDefault()
    {
        $this->manager->expects($this->never())
            ->method($this->anything());

        $this->assertTrue((new DefaultAwareManager($this->manager, $this->factory))->has(
            $this->createMock(ContentTypeInterface::class),
            'default'
        ));
    }

    public function testAll()
    {
        $this->manager->expects($this->once())
            ->method('all')
            ->with($this->equalTo(null))
            ->willReturn($iterator = new ArrayIterator());

        $this->assertSame($iterator, (new DefaultAwareManager($this->manager, $this->factory))->all());
    }

    public function testAllWithFilter()
    {
        $type = $this->createMock(ContentTypeInterface::class);

        $this->manager->expects($this->once())
            ->method('all')
            ->with($this->equalTo($type))
            ->willReturn($iterator = new ArrayIterator());

        $result = (new DefaultAwareManager($this->manager, $this->factory))->all($type);

        $this->assertNotSame($iterator, $result);
        $this->assertInstanceOf(DefaultAwareIterator::class, $result);
    }

    public function testRemove()
    {
        $config = [
            $this->createMock(FormConfigInterface::class),
            $this->createMock(FormConfigInterface::class),
        ];

        $this->manager->expects($this->exactly(2))
            ->method('remove')
            ->withConsecutive([$this->identicalTo($config[0])], [$this->identicalTo($config[1])]);

        $manager = new DefaultAwareManager($this->manager, $this->factory);

        $manager->remove($config[0]);
        $manager->remove($config[1]);
    }

    public function testSave()
    {
        $config = [
            $this->createMock(FormConfigInterface::class),
            $this->createMock(FormConfigInterface::class),
        ];

        $this->manager->expects($this->exactly(2))
            ->method('save')
            ->withConsecutive([$this->identicalTo($config[0])], [$this->identicalTo($config[1])]);

        $manager = new DefaultAwareManager($this->manager, $this->factory);

        $manager->save($config[0]);
        $manager->save($config[1]);
    }
}
