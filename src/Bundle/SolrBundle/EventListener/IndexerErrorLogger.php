<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\EventListener;

use Psr\Log\LoggerInterface;
use Integrated\Common\Solr\Indexer\Event\ErrorEvent;
use Integrated\Common\Solr\Indexer\Events;
use Integrated\Common\Solr\Indexer\Job;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class IndexerErrorLogger implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger = null;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::ERROR => 'onError',
        ];
    }

    /**
     * @param ErrorEvent $event
     */
    public function onError(ErrorEvent $event)
    {
        if (null === $this->logger) {
            return;
        }

        $payload = $event->getMessage()->getPayload();

        if ($payload instanceof Job) {
            $this->logger->error($event->getException()->getMessage(), [
                'action' => $payload->getAction(),
                'options' => $payload->getOptions(),
            ]);
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
