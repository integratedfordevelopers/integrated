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

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Channel\Exporter\Queue\RequestSerializerInterface;
use Integrated\Common\Content\ChannelableInterface;
use Integrated\Common\Content\PublishableInterface;
use Integrated\Common\Content\PublishTimeInterface;
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

        if (!$document instanceof PublishableInterface) {
            $this->process($document, 'add');

            return;
        }

        $publishTime = $document->getPublishTime();

        if ($document->isPublished(false)) {
            $this->process($document, 'add', $this->getDelay($publishTime->getStartDate()));

            $maxDate = new DateTime(PublishTimeInterface::DATE_MAX);

            if ($publishTime->getEndDate() && $publishTime->getEndDate() != $maxDate) {
                $this->process($document, 'delete', $this->getDelay($publishTime->getEndDate()));
            }
        } else {
            $this->process($document, 'delete');
        }
    }

    /**
     * @param ChannelableInterface $document
     * @param string               $state
     * @param int                  $delay
     */
    protected function process(ChannelableInterface $document, $state, $delay = 0)
    {
        $request = new Request();

        $request->content = $document;
        $request->state = $state;

        foreach ($document->getChannels() as $channel) {
            $request->channel = $channel;

            $this->queue->push($this->serializer->serialize($request), $delay);
        }
    }

    /**
     * @param DateTime|null $date
     *
     * @return int
     */
    private function getDelay(DateTime $date = null): int
    {
        $now = DateTime::createFromFormat('U', time()); // Needed for testing

        if (!$date || $date <= $now) {
            return 0;
        }

        return $date->getTimestamp() - $now->getTimestamp();
    }
}
