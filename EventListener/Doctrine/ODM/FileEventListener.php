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
use Integrated\Bundle\StorageBundle\Doctrine\ODM\Transformer\StorageIntentTransformer;
use Integrated\Bundle\StorageBundle\Storage\Command\DeleteCommand;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Content\Document\Storage\FileInterface;
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
     * @var StorageIntentTransformer
     */
    private $intentTransformer;

    /**
     * @param ManagerInterface $manager
     * @param FilesystemRemove $filesystemRemove
     * @param StorageIntentTransformer $intentTransformer
     */
    public function __construct(ManagerInterface $manager, FilesystemRemove $filesystemRemove, StorageIntentTransformer $intentTransformer)
    {
        $this->manager = $manager;
        $this->filesystemRemove = $filesystemRemove;
        $this->intentTransformer = $intentTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::prePersist,
            Events::preFlush,
            Events::onFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->intentTransformer->storageWrite($args->getDocumentManager()->getUnitOfWork());
    }

    /**
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $this->intentTransformer->storageWrite($args->getDocumentManager()->getUnitOfWork());
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
     * This event will be called on any flush to doctrine
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        // Keeps track of documents in current queue
        $uow = $args->getDocumentManager()->getUnitOfWork();

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
