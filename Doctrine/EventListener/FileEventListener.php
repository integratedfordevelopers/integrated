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

use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Document\File;
use Integrated\Bundle\StorageBundle\Storage\Command\DeleteCommand;
use Integrated\Bundle\StorageBundle\Storage\Manager;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileEventListener implements EventSubscriber
{
    /**
     * @const Repository class
     */
    const REPOSITORY = 'Integrated\Bundle\ContentBundle\Document\Content\Content';

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
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
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        // The entity which will be deleted
        $document = $args->getObject();

        if ($document instanceof File) {
            $this->filesystemDelete($args->getDocumentManager(), $document->getFile());
        }
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        // Keeps track of documents in current queue
        $uow = $args->getDocumentManager()->getUnitOfWork();

        foreach ($uow->getScheduledDocumentDeletions() as $entity) {
            if ($entity instanceof Storage) {
                $this->filesystemDelete($args->getDocumentManager(), $entity);
            }
        }
    }

    /**
     * We can only remove storage objects from the filesystem.
     *
     * @param DocumentManager $dm
     * @param Storage $storage
     */
    protected function filesystemDelete(DocumentManager $dm, Storage $storage)
    {
        // Query
        $repository = $dm->getRepository(self::REPOSITORY);
        $result = $repository->createQueryBuilder()
            ->field('file.identifier')->equals($storage->getIdentifier())
            ->getQuery()->execute();

        // Only delete when there is less than 2 documents (1 is the entity to deleted it self)
        if (2 < $result->count()) {
            // Lets put the delete command in a bus and send it away
            $this->manager->handle(
                new DeleteCommand($storage)
            );
        }
    }
}
