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

use Integrated\Common\Solr\Task\Event\ErrorEvent;
use Integrated\Common\Solr\Task\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkerErrorLogger implements EventSubscriberInterface
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
            Events::ERROR => 'onError'
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

        $this->logger->error($event->getException()->getMessage(), [
            'payload' => serialize($event->getMessage()->getPayload())
        ]);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
}
