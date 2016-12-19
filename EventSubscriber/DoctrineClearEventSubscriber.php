<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\SolrBundle\EventSubscriber;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;

use Integrated\Common\Solr\Indexer\Event\MessageEvent;
use Integrated\Common\Solr\Indexer\Events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class DoctrineClearEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param DocumentManager $documentManager
     * @param EntityManager $entityManager
     */
    public function __construct(DocumentManager $documentManager, EntityManager $entityManager)
    {
        $this->documentManager = $documentManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::PROCESSED => 'processedEvent'
        ];
    }

    /**
     * @param MessageEvent $messageEvent
     */
    public function processedEvent(MessageEvent $messageEvent)
    {
        $this->documentManager->clear();
        $this->entityManager->clear();
    }
}
