<?php

namespace Integrated\Bundle\ContentBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\SearchSelection\SearchSelection;
use Integrated\Bundle\ContentBundle\Services\SearchContentReferenced;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CheckReferencedListener implements EventSubscriber
{

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();

        if ($document instanceof Content || $document instanceof SearchSelection) {
            $dm = $args->getDocumentManager();
            $searchReferenced = new SearchContentReferenced($dm);
            if ($searchReferenced->getReferenced($document)) {
                throw new AccessDeniedException();
            }
        }
    }
}
