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
use Integrated\Common\ContentType\ContentTypeInterface;

/**
 * Clean up relations after removal of a content type.
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class CleanRelationsListener implements EventSubscriber
{
    public const RELATION_DOCUMENT = 'Integrated\Bundle\ContentBundle\Document\Relation\Relation';

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
        $documentManager = $args->getDocumentManager();

        // Document must be a ContentType
        if ($document instanceof ContentTypeInterface) {
            // Create update query
            $queryBuilder = $documentManager->createQueryBuilder(self::RELATION_DOCUMENT);

            $queryBuilder
                ->updateMany()

                ->addOr($queryBuilder->expr()->field('sources.$id')->equals($document->getId()))
                ->addOr($queryBuilder->expr()->field('targets.$id')->equals($document->getId()))

                ->field('sources')->pull(['$id' => $document->getId()])
                ->field('targets')->pull(['$id' => $document->getId()])

                ->getQuery()
                ->execute()
            ;
        }
    }
}
