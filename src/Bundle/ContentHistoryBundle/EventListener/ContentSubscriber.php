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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Integrated\Bundle\ContentHistoryBundle\Event\ContentHistoryEvent;
use Integrated\Bundle\ContentHistoryBundle\Diff\ArrayComparer;
use Integrated\Bundle\ContentHistoryBundle\Doctrine\ODM\MongoDB\Persister\PersistenceBuilder;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class ContentSubscriber implements EventSubscriberInterface
{
    /**
     * @var PersistenceBuilder
     */
    protected $persistenceBuilder;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @param DocumentManager $documentManager
     */
    public function __construct(DocumentManager $documentManager)
    {
        $this->persistenceBuilder = new PersistenceBuilder($documentManager);
        $this->documentManager = $documentManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ContentHistoryEvent::INSERT => 'onInsert',
            ContentHistoryEvent::UPDATE => 'onUpdate',
            ContentHistoryEvent::DELETE => 'onDelete',
        ];
    }

    /**
     * @param ContentHistoryEvent $event
     */
    public function onInsert(ContentHistoryEvent $event)
    {
        $event->getContentHistory()->setChangeSet(
            $this->persistenceBuilder->prepareData($event->getDocument())
        );
    }

    /**
     * @param ContentHistoryEvent $event
     */
    public function onUpdate(ContentHistoryEvent $event)
    {
        $document = $event->getDocument();

        $event->getContentHistory()->setChangeSet(
            ArrayComparer::diff($event->getOriginalData(), $this->persistenceBuilder->prepareData($document))
        );
    }

    /**
     * @param ContentHistoryEvent $event
     */
    public function onDelete(ContentHistoryEvent $event)
    {
        $event->getContentHistory()->setChangeSet(
            $event->getOriginalData()
        );
    }
}
