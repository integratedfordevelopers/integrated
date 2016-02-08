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
        // @todo priority

        return [
//            Events::postPersist,
//            Events::postUpdate,
            Events::onFlush,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function onFlush($args)
    {
        dump($args->getDocumentManager()->getUnitOfWork()); die;
//        $this->createLog($args->getDocumentManager(), $args->getDocumentManager()->getUnitOfWork()->getScheduledDocumentUpdates());

        //dump($args->getDocumentManager()->getUnitOfWork()->g);
        dump($args->getDocumentManager()->getUnitOfWork()->getScheduledDocumentInsertions());
        dump($args->getDocumentManager()->getUnitOfWork()->getScheduledDocumentDeletions());
        die;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->createLog($args, ContentHistory::ACTION_INSERT);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->createLog($args, ContentHistory::ACTION_UPDATE);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->createLog($args, ContentHistory::ACTION_DELETE);
    }

    /**
     * @param LifecycleEventArgs $args
     * @param string $action
     * @return bool
     */
    protected function createLog($dm, $docs)
    {
        if (!isset($GLOBALS['done'])) {
            $GLOBALS['done'] = array();
        }

//        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../log.txt", date("YmdHis") . get_class($args->getDocument()) . "\n", FILE_APPEND);
//        if (!$args->getDocument() instanceof ContentInterface) {
//            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../log.txt", date("YmdHis") . "return " . get_class($args->getDocument()) . "\n", FILE_APPEND);
//            return false;
//        }

//        if ($args->getDocument()->getTitle() == 'flap') {
//            dump($args);
//            exit;
//            return false;
//        }

        //$GLOBALS['done'][$args->getDocument()->getId()] = $args->getDocument()->getId();
        // $args->getDocument()->setTitle('flap');


        foreach ($docs as $doc) {
            if (!$doc instanceof ContentInterface) {
                continue;
            }

       // $dm = $args->getDocumentManager();
        //$dm->detach($args->getDocument());

        //dump($dm->getUnitOfWork()->getScheduledCollections($args->getDocument()));

        $history = new ContentHistory();

        //$history->setAction($action);
        $history->setSnapshot($doc);

        $dm->persist($history);
        $dm->flush($history);
    }

        return true;
    }
}
