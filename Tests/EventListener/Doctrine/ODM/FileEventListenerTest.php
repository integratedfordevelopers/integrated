<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Tests\EventListener\Doctrine\ODM;

use Integrated\Bundle\StorageBundle\EventListener\Doctrine\ODM\FileEventListener;

use Doctrine\ODM\MongoDB\Events;

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
        $listener = $this->getMockBuilder('Integrated\Bundle\StorageBundle\EventListener\Doctrine\ODM\FileEventListener')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->assertContains(Events::preRemove, $listener->getSubscribedEvents());
        $this->assertContains(Events::onFlush, $listener->getSubscribedEvents());
    }

    /**
     * Tests (events) of a document which not is allowed to delete won't be deleted
     */
    public function testBlockedRemove()
    {
        /**
         * @var \Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove|\PHPUnit_Framework_MockObject_MockObject $filesystemRemove
         */
        $filesystemRemove = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove')
            ->disableOriginalConstructor()
            ->getMock();
        $filesystemRemove->expects($this->any())
            ->method('allow')
            ->willReturn(false);

        /** @var \Integrated\Common\Storage\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMock('Integrated\Common\Storage\ManagerInterface');
        $manager->expects($this->never())
            ->method('delete');

        // Various usages
        $file = $this->getMock('Integrated\Common\Content\Document\Storage\FileInterface');
        $file->expects($this->any())
            ->method('getFile')
            ->willReturn(
                $this->getMock('Integrated\Common\Content\Document\Storage\Embedded\StorageInterface')
            );

        /**
         * @var \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs|\PHPUnit_Framework_MockObject_MockObject
         */
        $preRemove = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $preRemove->expects($this->once())
            ->method('getObject')
            ->willReturn($file);

        $uow = $this->getMockBuilder('Doctrine\ODM\MongoDB\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();
        $uow->expects($this->once())
            ->method('getScheduledDocumentDeletions')
            ->willReturn([$this->getMock('Integrated\Common\Content\Document\Storage\Embedded\StorageInterface')]);
        $dm = $this->getMockBuilder('\Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();
        $dm->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($uow);

        $onFlush = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\OnFlushEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $onFlush->expects($this->once())
            ->method('getDocumentManager')
            ->willReturn($dm);

        // The tests
        $listener = new FileEventListener($manager, $filesystemRemove);
        $listener->preRemove($preRemove);
        $listener->onFlush($onFlush);
    }

    /**
     * Test the doctrine on flush event
     */
    public function testOnFlushEvent()
    {
        /**
         * @var \Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove|\PHPUnit_Framework_MockObject_MockObject $filesystemRemove
         */
        $filesystemRemove = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove')
            ->disableOriginalConstructor()
            ->getMock();
        $filesystemRemove->expects($this->once())
            ->method('allow')
            ->willReturn(true);

        /** @var \Integrated\Common\Storage\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMock('Integrated\Common\Storage\ManagerInterface');
        $manager->expects($this->once())
            // Check if a delete command is posted
            ->method('handle');

        $uow = $this->getMockBuilder('Doctrine\ODM\MongoDB\UnitOfWork')
            ->disableOriginalConstructor()
            ->getMock();
        $uow->expects($this->once())
            ->method('getScheduledDocumentDeletions')
            ->willReturn([$this->getMock('Integrated\Common\Content\Document\Storage\Embedded\StorageInterface')]);
        $dm = $this->getMockBuilder('\Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();
        $dm->expects($this->once())
            ->method('getUnitOfWork')
            ->willReturn($uow);

        $onFlush = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\OnFlushEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $onFlush->expects($this->once())
            ->method('getDocumentManager')
            ->willReturn($dm);

        $listener = new FileEventListener($manager, $filesystemRemove);
        $listener->onFlush($onFlush);
    }

    /**
     * Test the doctrine pre remove event
     */
    public function testPreRemoveEvent()
    {
        /**
         * @var \Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove|\PHPUnit_Framework_MockObject_MockObject $filesystemRemove
         */
        $filesystemRemove = $this->getMockBuilder('Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove')
            ->disableOriginalConstructor()
            ->getMock();
        $filesystemRemove->expects($this->once())
            ->method('allow')
            ->willReturn(true);

        /** @var \Integrated\Common\Storage\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMock('Integrated\Common\Storage\ManagerInterface');
        $manager->expects($this->once())
            // Check if a delete command is posted
            ->method('handle');

        $file = $this->getMock('Integrated\Common\Content\Document\Storage\FileInterface');
        $file->expects($this->any())
            ->method('getFile')
            ->willReturn(
                $this->getMock('Integrated\Common\Content\Document\Storage\Embedded\StorageInterface')
            );

        /**
         * @var \Doctrine\ODM\MongoDB\Event\LifecycleEventArgs|\PHPUnit_Framework_MockObject_MockObject
         */
        $preRemove = $this->getMockBuilder('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $preRemove->expects($this->once())
            ->method('getObject')
            ->willReturn($file);

        // The test
        $listener = new FileEventListener($manager, $filesystemRemove);
        $listener->preRemove($preRemove);
    }
}
