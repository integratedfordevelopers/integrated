<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentHistoryBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistorySubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function onFlush($args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        //$this->createLog($dm, $uow->getScheduledDocumentInsertions(), ContentHistory::ACTION_INSERT);
        $this->createLog($dm, $uow->getScheduledDocumentUpdates(), ContentHistory::ACTION_UPDATE);
        //$this->createLog($dm, $uow->getScheduledDocumentDeletions(), ContentHistory::ACTION_DELETE);
    }

    /**
     * @param DocumentManager $dm
     * @param array $documents
     * @param string $action
     */
    protected function createLog(DocumentManager $dm, array $documents, $action)
    {
        $uow = $dm->getUnitOfWork();

        foreach ($documents as $document) {
            $changeSet = $uow->getDocumentChangeSet($document);

            unset($changeSet['relations']); // @hack

            if (count($changeSet)) {
                $history = new ContentHistory();

                $history->setAction($action);
                $history->setChangeSet($changeSet);

                $dm->persist($history);
                $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($history)), $history);
            }
        }
    }
}
