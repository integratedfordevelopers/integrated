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

use Integrated\Bundle\StorageBundle\Storage\Command\DeleteCommand;
use Integrated\Common\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Document\Storage\FileInterface;
use Integrated\Common\Storage\ManagerInterface;

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
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
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
        // The document which will be deleted
        $document = $args->getObject();

        if ($document instanceof FileInterface) {
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

        foreach ($uow->getScheduledDocumentDeletions() as $document) {
            if ($document instanceof StorageInterface) {
                $this->filesystemDelete($args->getDocumentManager(), $document);
            }
        }
    }

    /**
     * Remove a file from the filesystem when its not used by any other documents
     *
     * @param DocumentManager $dm
     * @param StorageInterface $storage
     */
    protected function filesystemDelete(DocumentManager $dm, StorageInterface $storage)
    {
        // Query on the file identifier (unique/hash based filename)
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
