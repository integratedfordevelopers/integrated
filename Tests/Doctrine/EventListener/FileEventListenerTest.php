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
use Integrated\Bundle\StorageBundle\Tests\Document\Embedded\StorageTest;

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

        $mockDeleteStorage = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\Storage')
            ->disableOriginalConstructor()
            ->getMock();

        $mockDeleteStorage
            ->expects($this->once())
            ->method('delete')
        ;

        $file = new File();
        $file->setFile(
            $this->getMockBuilder('Integrated\Bundle\StorageBundle\Document\Embedded\Storage')
            ->disableOriginalConstructor()
            ->getMock()
        );

        $event = new FileEventListener($mockDeleteStorage);
        $event->preRemove(new LifecycleEventArgs(
            $file,
            $mockDocumentManager
        ));
    }

    /**
     * Test the doctrine on flush event
     */
    public function testOnFlushEvent()
    {
        /** @var \Doctrine\ODM\MongoDB\UnitOfWork|\PHPUnit_Framework_MockObject_MockObject $mockUow */
        $mockUow = $this->getMockBuilder('Doctrine\ODM\MongoDB\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();

        $mockUow
            ->expects($this->once())
            ->method('getScheduledDocumentDeletions')
            ->willReturn([StorageTest::createObject($this, 'onFlushEvent')])
        ;

        /** @var \Doctrine\ODM\MongoDB\DocumentManager|\PHPUnit_Framework_MockObject_MockObject $mockDocumentManager */
        $mockDocumentManager = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockDocumentManager
            ->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($mockUow);

        $mockDeleteStorage = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\Storage')
            ->disableOriginalConstructor()
            ->getMock();

        $mockDeleteStorage
            ->expects($this->once())
            ->method('delete')
        ;

        $event = new FileEventListener($mockDeleteStorage);
        $event->onFlush(new OnFlushEventArgs($mockDocumentManager));
    }
}
