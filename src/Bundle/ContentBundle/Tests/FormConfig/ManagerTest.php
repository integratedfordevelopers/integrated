<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\FormConfig;

use ArrayIterator;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Query;
use Integrated\Bundle\ContentBundle\Document\FormConfig\FormConfig;
use Integrated\Bundle\ContentBundle\FormConfig\Manager;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\FormConfig\Exception\NotFoundException;
use Integrated\Common\FormConfig\Exception\UnexpectedTypeException;
use Integrated\Common\FormConfig\FormConfigInterface;
use Integrated\Common\FormConfig\FormConfigManagerInterface;

class ManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DocumentManager | \PHPUnit\Framework\MockObject\MockObject
     */
    private $manager;

    protected function setUp()
    {
        $this->manager = $this->getMockBuilder(DocumentManager::class)->disableOriginalConstructor()->getMock();
    }

    public function testInterface()
    {
        $this->assertInstanceOf(FormConfigManagerInterface::class, new Manager($this->manager));
    }

    public function testGet()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('content-type');

        $config = $this->getMockBuilder(FormConfig::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn($config);

        $this->assertSame($config, (new Manager($this->manager))->get($type, 'key'));
    }

    public function testGetNotFound()
    {
        $this->expectException(NotFoundException::class);

        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('content-type');

        $this->manager->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $manager = new Manager($this->manager);
        $manager->get($type, 'key');
    }

    public function testHas()
    {
        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('content-type');

        $config = $this->getMockBuilder(FormConfig::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->exactly(2))
            ->method('find')
            ->willReturnOnConsecutiveCalls($config, null);

        $manager = new Manager($this->manager);

        $this->assertTrue($manager->has($type, 'key'));
        $this->assertFalse($manager->has($type, 'key'));
    }

    public function testAll()
    {
        $iterator = new ArrayIterator();

        $query = $this->getMockBuilder(Query::class)->disableOriginalConstructor()->getMock();
        $query->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $builder = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$this->createMock(DocumentManager::class)])
            ->setMethods(['getQuery'])
            ->getMock();

        $builder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $this->manager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($builder);

        $this->assertSame($iterator, (new Manager($this->manager))->all());
    }

    public function testAllWithFilter()
    {
        $iterator = new ArrayIterator();

        $query = $this->getMockBuilder(Query::class)->disableOriginalConstructor()->getMock();
        $query->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $builder = $this->getMockBuilder(Builder::class)
            ->setConstructorArgs([$this->createMock(DocumentManager::class)])
            ->setMethods(['getQuery'])
            ->getMock();

        $builder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $this->manager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($builder);

        $type = $this->createMock(ContentTypeInterface::class);
        $type->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('content-type');

        $this->assertSame($iterator, (new Manager($this->manager))->all($type));
    }

    public function testRemove()
    {
        $config = $this->getMockBuilder(FormConfig::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($config));

        $this->manager->expects($this->once())
            ->method('flush')
            ->with($this->identicalTo($config));

        $manager = new Manager($this->manager);
        $manager->remove($config);
    }

    public function testRemoveUnsupportedConfig()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->manager->expects($this->never())
            ->method($this->anything());

        $manager = new Manager($this->manager);
        $manager->remove($this->createMock(FormConfigInterface::class));
    }

    public function testSave()
    {
        $config = $this->getMockBuilder(FormConfig::class)->disableOriginalConstructor()->getMock();

        $this->manager->expects($this->once())
            ->method('flush')
            ->with($this->identicalTo($config));

        $manager = new Manager($this->manager);
        $manager->save($config);
    }

    public function testSaveUnsupportedConfig()
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->manager->expects($this->never())
            ->method($this->anything());

        $manager = new Manager($this->manager);
        $manager->save($this->createMock(FormConfigInterface::class));
    }
}
