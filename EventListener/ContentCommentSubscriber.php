<?php

namespace Integrated\Bundle\CommentBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\CommentBundle\Service\ContentCommentTransformer;
use Integrated\Bundle\ContentBundle\Document\Content\Article;

/**
 * Class ContentCommentSubscriber
 */
class ContentCommentSubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
            Events::postLoad,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();

        if (!$document instanceof Article) {
            return;
        }

        $contentTransformer = new ContentCommentTransformer($args->getDocumentManager());
        $contentTransformer->parseComments($document);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();

        if (!$document instanceof Article) {
            return;
        }

        $contentTransformer = new ContentCommentTransformer($args->getDocumentManager());
        $contentTransformer->setComments($document);
    }
}
