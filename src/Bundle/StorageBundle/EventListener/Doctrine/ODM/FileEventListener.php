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
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PreFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\StorageBundle\Doctrine\ODM\Transformer\StorageIntentTransformer;
use Integrated\Bundle\StorageBundle\Storage\Accessor\DoctrineDocument;
use Integrated\Common\Storage\ManagerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class FileEventListener implements EventSubscriber
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var StorageIntentTransformer
     */
    private $intentTransformer;

    /**
     * @param ManagerInterface         $manager
     * @param StorageIntentTransformer $intentTransformer
     */
    public function __construct(ManagerInterface $manager, StorageIntentTransformer $intentTransformer)
    {
        $this->manager = $manager;
        $this->intentTransformer = $intentTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preFlush,
        ];
    }

    /**
     * This event will be called on a document persist.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->intentTransformer->transform(new DoctrineDocument($args->getDocument()));
    }

    /**
     * This event will be called on any flush in doctrine.
     *
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        // Keeps track of the documents in the current queue
        $uow = $args->getDocumentManager()->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $identities) {
            foreach ($identities as $document) {
                //skip unloaded proxies, they cannot contain a StoreIntentUpload
                if ($document instanceof Proxy && !$document->__isInitialized()) {
                    continue;
                }

                // Use a proxy for the transformer data
                $proxyDocument = new DoctrineDocument($document);
                $this->intentTransformer->transform($proxyDocument);

                // Only reschedule the update when we've made changes
                if ($proxyDocument->hasUpdates()) {
                    $uow->scheduleForUpdate($document);
                }
            }
        }
    }
}
