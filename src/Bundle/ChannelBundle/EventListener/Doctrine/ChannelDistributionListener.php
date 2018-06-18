<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Channel\Exporter\Queue\RequestSerializerInterface;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Queue\QueueInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ChannelDistributionListener implements EventSubscriber
{
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var RequestSerializerInterface
     */
    private $serializer;

    /**
     * @param QueueInterface             $queue
     * @param RequestSerializerInterface $serializer
     */
    public function __construct(QueueInterface $queue, RequestSerializerInterface $serializer)
    {
        $this->queue = $queue;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postRemove,
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postRemove(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();

        if (!$document instanceof ChannelableInterface) {
            return;
        }

        $this->process($document, 'delete');
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        $this->postUpdate($event);
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();

        if (!$document instanceof ChannelableInterface) {
            return;
        }

        $state = 'add';

        if ($document instanceof Content && !$document->isPublished()) {
            $state = 'delete';
        }

        $this->process($document, $state);
    }

    /**
     * @param ChannelableInterface $document
     * @param string               $state
     */
    protected function process(ChannelableInterface $document, $state)
    {
        $request = new Request();

        $request->content = $document;
        $request->state = $state;

        foreach ($document->getChannels() as $channel) {
            $request->channel = $channel;

            $this->queue->push($this->serializer->serialize($request));
        }
    }
}
