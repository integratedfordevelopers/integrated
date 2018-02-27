<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Solr\Task\Tasks\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Solr\Task\Tasks\Doctrine\MongoDBReferenceQueueTask;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MongoDBReferencesListener implements EventSubscriber
{
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * constructor.
     *
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $this->process($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->process($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    protected function process(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();

        if (!$document instanceof ContentInterface) {
            return;
        }

        $this->queue->push(new MongoDBReferenceQueueTask($document->getId()));
    }
}
