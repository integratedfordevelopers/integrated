<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Doctrine\EventListener;

use Integrated\Bundle\StorageBundle\Document\File;
use Integrated\Bundle\StorageBundle\Document\Embedded\Storage as EmbeddedStorage;
use Integrated\Bundle\StorageBundle\Doctrine\Storage;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileEventListener implements EventSubscriber
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::onFlush
        ];
    }

    /**
     * This event will be called when a entity is deleted
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        // The entity which will be deleted
        $document = $args->getObject();

        if ($document instanceof File) {
            $this->storage->delete($args->getDocumentManager(), $document->getFile());
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

        foreach ($uow->getScheduledDocumentDeletions() as $entity) {
            if ($entity instanceof EmbeddedStorage) {
                $this->storage->delete($args->getDocumentManager(), $entity);
            }
        }
    }
}
