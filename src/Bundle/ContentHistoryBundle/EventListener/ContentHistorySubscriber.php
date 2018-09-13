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
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\ContentHistoryBundle\Event\ContentHistoryEvent;
use Integrated\Common\Content\ContentInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentHistorySubscriber implements EventSubscriber
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $className;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param string          $className
     */
    public function __construct(EventDispatcher $eventDispatcher, $className)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->className = $className;
    }

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
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        $this->dispatch($dm, $uow->getScheduledDocumentInsertions(), ContentHistoryEvent::INSERT);
        $this->dispatch($dm, $uow->getScheduledDocumentUpdates(), ContentHistoryEvent::UPDATE);
        $this->dispatch($dm, $uow->getScheduledDocumentDeletions(), ContentHistoryEvent::DELETE);
    }

    /**
     * @param DocumentManager $dm
     * @param array           $documents
     * @param string          $action
     */
    protected function dispatch(DocumentManager $dm, array $documents, $action)
    {
        $classMetadata = $dm->getClassMetadata($this->className);

        foreach ($documents as $document) {
            if (!$document instanceof ContentInterface) {
                continue;
            }

            $history = new $this->className($document, $action);
            $originalData = $this->getOriginalData($dm, $document, $action);

            $this->eventDispatcher->dispatch($action, new ContentHistoryEvent($history, $document, $originalData));

            $dm->persist($history);
            $dm->getUnitOfWork()->recomputeSingleDocumentChangeSet($classMetadata, $history);
        }
    }

    /**
     * @param DocumentManager  $dm
     * @param ContentInterface $document
     * @param string           $action
     *
     * @return array
     */
    protected function getOriginalData(DocumentManager $dm, ContentInterface $document, $action)
    {
        if ($action == ContentHistoryEvent::INSERT) {
            return [];
        }

        return (array) $dm->createQueryBuilder(\get_class($document))->hydrate(false)
            ->field('id')->equals($document->getId())
            ->getQuery()->getSingleResult();
    }
}
