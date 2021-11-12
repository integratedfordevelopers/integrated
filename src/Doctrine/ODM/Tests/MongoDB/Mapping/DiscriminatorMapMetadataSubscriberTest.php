<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Doctrine\ODM\Tests\MongoDB\Mapping;

use Doctrine\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Integrated\Doctrine\ODM\MongoDB\Mapping\DiscriminatorMapMetadataSubscriber;
use Integrated\Doctrine\ODM\MongoDB\Mapping\DiscriminatorMapResolverInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DiscriminatorMapMetadataSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DiscriminatorMapResolverInterface|MockObject
     */
    private $resolver;

    protected function setUp(): void
    {
        $this->resolver = $this->createMock('Integrated\\Doctrine\\ODM\\MongoDB\\Mapping\\DiscriminatorMapResolverInterface');
    }

    public function testInterface()
    {
        self::assertInstanceOf('Doctrine\\Common\\EventSubscriber', $this->getInstance());
    }

    public function testGetSubscribedEvents()
    {
        self::assertEquals([Events::loadClassMetadata], $this->getInstance()->getSubscribedEvents());
    }

    public function testLoadClassMetadata()
    {
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with($this->equalTo('classname'))
            ->willReturn(['classname1' => 'classname1', 'classname2' => 'classname2']);

        $metadata = $this->getMetadata('classname');
        $metadata->expects($this->once())
            ->method('setDiscriminatorMap')
            ->with($this->equalTo(['classname1' => 'classname1', 'classname2' => 'classname2']));

        $this->getInstance()->loadClassMetadata($this->getEvent($metadata));

        // These values should have been reset, normally when the setDiscriminatorMap is not
        // be mocked away this would be filled by new values.

        self::assertEquals([], $metadata->discriminatorMap);
        self::assertNull($metadata->discriminatorValue);
        self::assertEquals([], $metadata->subClasses);
    }

    public function testLoadClassMetadataNoMongoDBMetadata()
    {
        $this->resolver->expects($this->never())
            ->method($this->anything());

        $this->getInstance()->loadClassMetadata($this->getEvent($this->createMock('Doctrine\\Persistence\\Mapping\\ClassMetadata')));
    }

    public function testLoadClassMetadataNoMapResolve()
    {
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with($this->equalTo('classname'))
            ->willReturn(null);

        $metadata = $this->getMetadata('classname');
        $metadata->expects($this->never())
            ->method('setDiscriminatorMap');

        $this->getInstance()->loadClassMetadata($this->getEvent($metadata));

        self::assertEquals(['this-is-not' => 'changed'], $metadata->discriminatorMap);
        self::assertEquals('not-empty', $metadata->discriminatorValue);
        self::assertEquals(['this', 'is', 'not', 'changed'], $metadata->subClasses);
    }

    /**
     * @return DiscriminatorMapMetadataSubscriber
     */
    protected function getInstance()
    {
        return new DiscriminatorMapMetadataSubscriber($this->resolver);
    }

    /**
     * @return LoadClassMetadataEventArgs|MockObject
     */
    protected function getEvent($metadata)
    {
        $mock = $this->getMockBuilder('Doctrine\Persistence\Event\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $mock->expects($this->atLeastOnce())
            ->method('getClassMetadata')
            ->willReturn($metadata);

        return $mock;
    }

    /**
     * @param string $name
     *
     * @return ClassMetadata|MockObject
     */
    protected function getMetadata($name)
    {
        $mock = $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->onlyMethods(['setDiscriminatorMap', 'getName'])
            ->getMock();

        $mock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($name);

        $mock->discriminatorMap = ['this-is-not' => 'changed'];
        $mock->discriminatorValue = 'not-empty';
        $mock->subClasses = ['this', 'is', 'not', 'changed'];

        return $mock;
    }
}
