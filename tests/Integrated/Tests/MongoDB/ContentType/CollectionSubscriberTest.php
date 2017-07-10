<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Tests\MongoDB\ContentType;

use Integrated\MongoDB\ContentType\CollectionSubscriber;
use Doctrine\ODM\MongoDB\Events;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CollectionSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectionSubscriber
     */
    private $subscriber;

    protected function setUp()
    {
        $this->subscriber = new CollectionSubscriber('stdClass', 'collection');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('Doctrine\Common\EventSubscriber', $this->subscriber);
    }

    public function testGetClass()
    {
        $this->assertEquals('stdClass', $this->subscriber->getClass());
    }

    public function testGetCollection()
    {
        $this->assertEquals('collection', $this->subscriber->getCollection());
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(array(Events::loadClassMetadata), $this->subscriber->getSubscribedEvents());
    }

    public function testloadClassMetadata()
    {
        $meta = $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo')
            ->setMethods(['setCollection'])
            ->setConstructorArgs(['stdClass'])
            ->getMock();

        $meta->expects($this->once())
            ->method('setCollection')
            ->with($this->identicalTo('collection'));

        $event = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->subscriber->loadClassMetadata($event);
    }

    public function testloadClassMetadataWithSubclass()
    {
        $class = $this->getMockClass('stdClass');

        $meta = $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo')->setMethods(['setCollection'])->setConstructorArgs([$class])->getMock();
        $meta->expects($this->never())
            ->method('setCollection');

        $event = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->subscriber->loadClassMetadata($event);
    }

    public function testloadClassMetadataWithInvalidClass()
    {
        $meta = $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo')
            ->setMethods(['setCollection'])
            ->setConstructorArgs(['ArrayObject'])
            ->getMock();

        $meta->expects($this->never())
            ->method('setCollection');

        $event = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs')->disableOriginalConstructor()->getMock();
        $event->expects($this->atLeastOnce())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->subscriber->loadClassMetadata($event);
    }
}
