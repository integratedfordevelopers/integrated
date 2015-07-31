<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Tests\Doctrine\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\StorageBundle\Doctrine\EventListener\FileEventListener;
use Integrated\Bundle\StorageBundle\Document\File;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the subscribed events
     */
    public function testSubscribedEvents()
    {
        /** @var FileEventListener $listener */
        $listener = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\EventListener\FileEventListener')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertContains(Events::preRemove, $listener->getSubscribedEvents());
        $this->assertContains(Events::onFlush, $listener->getSubscribedEvents());
    }

    /**
     * Tests the doctrine pre remove event
     */
    public function testPreRemoveEvent()
    {
        /** @var \Doctrine\ODM\MongoDB\DocumentManager $mockDocumentManager */
        $mockDocumentManager = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockEvent = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\EventListener\FileEventListener')
            ->disableOriginalConstructor()
            ->setMethods(['filesystemDelete'])
            ->getMock();

        $mockEvent
            ->expects($this->once())
            ->method('filesystemDelete')
        ;

        $file = new File();
        $file->setFile(
            $this->getMockBuilder('Integrated\Bundle\StorageBundle\Document\Embedded\Storage')
            ->disableOriginalConstructor()
            ->getMock()
        );

        $mockEvent->preRemove(new LifecycleEventArgs(
            $file,
            $mockDocumentManager
        ));
    }

    /**
     * Test the doctrine on flush event
     */
    public function testOnFlushEvent()
    {
        /** @var \Integrated\Bundle\StorageBundle\Storage\Manager|\PHPUnit_Framework_MockObject_MockObject $storageManager */
        $storageManager = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Storage\Manager')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \PHPUnit_Framework_MockObject_MockObject $mockEvent */
        $mockEvent = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\EventListener\FileEventListener')
            ->setConstructorArgs([$storageManager])
            ->setMethods(['filesystemDelete'])
            ->getMock();

        $mockEvent
            ->expects($this->once())
            ->method('filesystemDelete')
        ;

        /** @var \Integrated\Bundle\StorageBundle\Document\Embedded\Storage|\PHPUnit_Framework_MockObject_MockObject $storageMock */
        $storageMock = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Document\Embedded\Storage')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Doctrine\ODM\MongoDB\UnitOfWork|\PHPUnit_Framework_MockObject_MockObject $mockUow */
        $mockUow = $this->getMockBuilder('Doctrine\ODM\MongoDB\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();

        $mockUow
            ->expects($this->once())
            ->method('getScheduledDocumentDeletions')
            ->willReturn([$storageMock]);

        /** @var \Doctrine\ODM\MongoDB\DocumentManager|\PHPUnit_Framework_MockObject_MockObject $mockDocumentManager */
        $mockDocumentManager = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockDocumentManager
            ->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($mockUow);

        $mockEvent->onFlush(new OnFlushEventArgs($mockDocumentManager));
    }
}
