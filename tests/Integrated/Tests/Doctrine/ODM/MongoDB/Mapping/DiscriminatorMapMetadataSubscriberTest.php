<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\Doctrine\ODM\MongoDB\Mapping;

use Integrated\Doctrine\ODM\MongoDB\Mapping\DiscriminatorMapMetadataSubscriber;
use Integrated\Doctrine\ODM\MongoDB\Mapping\DiscriminatorMapResolverInterface;

use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DiscriminatorMapMetadataSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DiscriminatorMapResolverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    protected function setUp()
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

        $this->getInstance()->loadClassMetadata($this->getEvent($this->createMock('Doctrine\\Common\\Persistence\\Mapping\\ClassMetadata')));
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
     * @return LoadClassMetadataEventArgs | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEvent($metadata)
    {
        $mock = $this->getMockBuilder('Doctrine\\ODM\\MongoDB\\Event\\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $mock->expects($this->atLeastOnce())
            ->method('getClassMetadata')
            ->willReturn($metadata);

        return $mock;
    }

    /**
     * @param string $name
     *
     * @return ClassMetadataInfo | \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMetadata($name)
    {
        $mock = $this->getMockBuilder('Doctrine\\ODM\\MongoDB\\Mapping\\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->setMethods(['setDiscriminatorMap', 'getName'])
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
