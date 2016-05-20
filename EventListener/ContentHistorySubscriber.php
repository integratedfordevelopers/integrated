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

use Integrated\Bundle\ContentHistoryBundle\Diff\ArrayComparer;
use Integrated\Bundle\ContentHistoryBundle\Doctrine\ODM\MongoDB\Persister\PersistenceBuilder;
use Integrated\Bundle\ContentHistoryBundle\Document\ContentHistory;
use Integrated\Common\Content\ContentInterface;

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

        $this->createLog($dm, $uow->getScheduledDocumentInsertions(), ContentHistory::ACTION_INSERT);
        $this->createLog($dm, $uow->getScheduledDocumentUpdates(), ContentHistory::ACTION_UPDATE);
        $this->createLog($dm, $uow->getScheduledDocumentDeletions(), ContentHistory::ACTION_DELETE);
    }

    /**
     * @param DocumentManager $dm
     * @param array $documents
     * @param string $action
     */
    protected function createLog(DocumentManager $dm, array $documents, $action)
    {
        $uow = $dm->getUnitOfWork();
        $pb = new PersistenceBuilder($dm);

        foreach ($documents as $document) {
            if (!$document instanceof ContentInterface) {
                continue;
            }

            $history = new ContentHistory($document->getId(), $action);

            $previous = $dm->createQueryBuilder(ContentHistory::class)
                ->field('contentId')->equals($history->getContentId())
                ->sort('date', 'desc')
                ->getQuery()->getSingleResult();

            // link previous content history document
            $history->setPrevious($previous);

            // load original data
            $originalData = (array) $dm->createQueryBuilder(get_class($document))->hydrate(false)
                ->field('id')->equals($document->getId())
                ->getQuery()->getSingleResult();

            $history->setChangeSet(ArrayComparer::diff($originalData, $pb->prepareData($document)));

            if (!count($history->getChangeSet())) {
                continue; // no changes
            }

            $dm->persist($history);
            $uow->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($history)), $history);
        }
    }
}
