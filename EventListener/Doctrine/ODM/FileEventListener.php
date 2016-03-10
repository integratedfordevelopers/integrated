<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\EventListener\Doctrine\ODM;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;

use Integrated\Bundle\StorageBundle\Doctrine\ODM\Event\Remove\FilesystemRemove;
use Integrated\Bundle\StorageBundle\Form\Upload\StorageIntentUpload;
use Integrated\Bundle\StorageBundle\Storage\Command\DeleteCommand;
use Integrated\Bundle\StorageBundle\Storage\Decision;
use Integrated\Bundle\StorageBundle\Storage\Reader\UploadedFileReader;
use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;
use Integrated\Bundle\StorageBundle\Storage\Reflection\Document\ManipulatorDocument;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Content\Document\Storage\FileInterface;
use Integrated\Common\Storage\DecisionInterface;
use Integrated\Common\Storage\ManagerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileEventListener implements EventSubscriber
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var FilesystemRemove
     */
    protected $filesystemRemove;

    /**
     * @var ReflectionCacheInterface
     */
    protected $reflection;

    /**
     * @var DecisionInterface
     */
    private $decision;

    /**
     * @param ManagerInterface $manager
     * @param FilesystemRemove $filesystemRemove
     * @param ReflectionCacheInterface $reflection
     * @param DecisionInterface $decision
     */
    public function __construct(ManagerInterface $manager, FilesystemRemove $filesystemRemove, ReflectionCacheInterface $reflection, DecisionInterface $decision)
    {
        $this->manager = $manager;
        $this->filesystemRemove = $filesystemRemove;
        $this->reflection = $reflection;
        $this->decision = $decision;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::preFlush,
            Events::onFlush,
        ];
    }

    /**
     * This event will be called when a entity is deleted
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        // The document which will be deleted
        $document = $args->getObject();

        if ($document instanceof FileInterface) {
            if ($this->filesystemRemove->allow($args->getDocumentManager(), $document->getFile())) {
                // Lets put the delete command in a bus and send it away
                $this->manager->handle(
                    new DeleteCommand($document->getFile())
                );
            }
        }
    }

    /**
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        // This is the mother of all children from the womb of Doctrine
        $uow = $args->getDocumentManager()->getUnitOfWork();

        // Process the full identity map
        foreach ($uow->getIdentityMap() as $class => $documents) {
            // This must be cached
            $reflection = $this->reflection->getPropertyReflectionClass($class);

            // Check if we've got anything on which we can reflect
            if ($properties = $reflection->getTargetProperties()) {
                foreach ($properties as $property) {
                    // Its less costly to reflect classes than documents
                    // Rather than do per class multiple documents
                    foreach ($documents as $id => $document) {
                        // Allow us to modify the document
                        $manipulator = new ManipulatorDocument($document);

                        // Extract the value
                        $value = $manipulator->get($property->getPropertyName());
                        if ($value instanceof StorageIntentUpload) {
                            // Remove the intent and make a storage object outta it
                            $manipulator->set(
                                $property->getPropertyName(),
                                // Create the file in the file system
                                $this->manager->write(
                                    new UploadedFileReader($value->getUploadedFile()),
                                    // Use the decision map to place the file in the correct (public or private) file systems
                                    $this->decision->getFilesystems($document)
                                )
                            );

                            // Tell doctrine we've changed something
                            $uow->scheduleForUpdate($document);
                        }
                    }
                }
            }
        }
    }

    /**
     * This event will be called on any flush to doctrine
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        // Keeps track of documents in current queue
        $uow = $args->getDocumentManager()->getUnitOfWork();
        $dm = $args->getDocumentManager();

        foreach ($uow->getScheduledDocumentDeletions() as $documentDeletion) {
            if ($documentDeletion instanceof StorageInterface) {
                if ($this->filesystemRemove->allow($args->getDocumentManager(), $documentDeletion)) {
                    // Lets put the delete command in a bus and send it away
                    $this->manager->handle(
                        new DeleteCommand($documentDeletion)
                    );
                }
            }
        }
    }
}
