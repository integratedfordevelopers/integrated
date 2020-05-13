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
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation;

class UpdateAuthorRelationListener implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $dm = $args->getDocumentManager();
        $uow = $dm->getUnitOfWork();

        foreach (array_merge($uow->getScheduledDocumentInsertions(), $uow->getScheduledDocumentUpdates()) as $document) {
            if ($document instanceof Article) {
                /** @var $document Article */
                $authors = [];
                foreach ($document->getAuthors() as $author) {
                    if ($author->getPerson() !== false) {
                        $authors[] = $author->getPerson();
                    }
                }
                if ($relation = $document->getRelation('__authors')) {
                    foreach ($relation->getReferences() as $reference) {
                        if (($key = array_search($reference, $authors)) !== false) {
                            unset($authors[$key]);
                        } else {
                            $relation->getReferences()->removeElement($reference);
                        }
                    }
                } elseif (\count($authors) > 0) {
                    $relation = new Relation();
                    $relation->setRelationId('__authors');
                    $relation->setRelationType('author');
                    $document->addRelation($relation);
                }

                foreach ($authors as $author) {
                    $document->getRelation('__authors')->addReference($author);
                }

                $class = $dm->getClassMetadata(\get_class($document));
                $uow->recomputeSingleDocumentChangeSet($class, $document);
            }
        }
    }
}
