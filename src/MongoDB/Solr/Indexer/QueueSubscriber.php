<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\Solr\Indexer;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Common\Content\ContentInterface;
use Integrated\Common\Queue\QueueAwareInterface;
use Integrated\Common\Queue\QueueInterface;
use Integrated\Common\Solr\Indexer\Job;
use Symfony\Component\Security\Acl\Util\ClassUtils;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class QueueSubscriber implements EventSubscriber, QueueAwareInterface, SerializerAwareInterface
{
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $format = null;

    /**
     * @var int
     */
    private $priority = 0;

    /**
     * @param QueueInterface      $queue
     * @param SerializerInterface $serializer
     * @param int                 $priority
     */
    public function __construct(QueueInterface $queue, SerializerInterface $serializer, $priority = 0)
    {
        $this->setQueue($queue);
        $this->setSerializer($serializer);
        $this->setPriority($priority);
    }

    /**
     * {@inheritdoc}
     */
    public function setQueue(QueueInterface $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @return QueueInterface
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * {@inheritdoc}
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param string $format
     */
    public function setSerializerFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @return string
     */
    public function getSerializerFormat()
    {
        if ($this->format === null) {
            $this->format = 'json';
        }

        return $this->format;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = (int) $priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        $this->process('ADD', $event);
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $this->process('ADD', $event);
    }

    public function postRemove(LifecycleEventArgs $event)
    {
        $this->process('DELETE', $event);
    }

    protected function process($action, LifecycleEventArgs $event)
    {
        $document = $event->getDocument();

        // @codeCoverageIgnoreStart
        if (!$document instanceof ContentInterface || !$document->getContentType() || !$document->getId()) {
            return;
        }
        // @codeCoverageIgnoreEnd

        $job = new Job($action);

        switch ($job->getAction()) {
            case 'ADD':
                // probably should make a solr document id generator service or something like that
                $job->setOption('document.id', $document->getContentType().'-'.$document->getId());

                $job->setOption('document.data', $this->getSerializer()->serialize($document, $this->getSerializerFormat()));
                $job->setOption('document.class', ClassUtils::getRealClass($document));
                $job->setOption('document.format', $this->getSerializerFormat());

                break;

            case 'DELETE':
                // probably should make a solr document id generator service or something like that
                $job->setOption('id', $document->getContentType().'-'.$document->getId());
                break;
        }

        $this->getQueue()->push($job, 0, $this->priority);

        if ($this->priority > QueueInterface::PRIORITY_MEDIUM_HIGH) {
            $this->getQueue()->push(new Job('COMMIT', ['softcommit' => 'true']), 0, $this->priority);

            // Do this only one time
            $this->setPriority(QueueInterface::PRIORITY_MEDIUM_HIGH);
        }
    }
}
