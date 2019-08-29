<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Storage;
use Integrated\Bundle\ContentBundle\Document\Content\File;

class UpdateStorageMetadataListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach (array_merge($uow->getScheduledDocumentInsertions(), $uow->getScheduledDocumentUpdates()) as $document) {
            if ($document instanceof File) {
                /** @var $document File */
                if ($document->getFile() instanceof Storage) {
                    $document->getFile()->getMetadata()->setCredits($document->getCredits());
                    $document->getFile()->getMetadata()->setDescription($document->getDescription());

                    $class = $dm->getClassMetadata(\get_class($document));
                    $uow->recomputeSingleDocumentChangeSet($class, $document);
                }
            }
        }
    }
}
