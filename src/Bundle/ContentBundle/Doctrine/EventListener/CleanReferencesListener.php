<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * Clean up references after removal of a document.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CleanReferencesListener implements EventSubscriber
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
        // Get document
        $document = $args->getDocument();

        // Get document manager
        $dm = $args->getDocumentManager();

        // Document must be instanceof Content
        if ($document instanceof Content) {
            $dm->createQueryBuilder(Content::class)
                ->updateMany()
                ->field('relations.references.$id')->equals($document->getId())
                ->field('relations.$.references')->pull(['$id' => $document->getId()])
                ->getQuery()
                ->execute();
        }
    }
}
