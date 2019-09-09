<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Solarium\QueryType\Select\Result\DocumentInterface;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;
use Integrated\Common\Content\ContentInterface;

/**
 * Class ContentRepository.
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class ContentRepository extends DocumentRepository
{
    /**
     * Get items which have the current document linked.
     *
     * @param ArrayCollection $content
     * @param Relation|null   $relation
     * @param Content|null    $excludeContent
     * @param bool            $filterPublished
     *
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getUsedBy(ArrayCollection $content, Relation $relation = null, Content $excludeContent = null, $filterPublished = true)
    {
        $contentIds = [];
        foreach ($content as $contentItem) {
            if ($contentItem instanceof ContentInterface) {
                if (!$excludeContent) {
                    $excludeContent = $contentItem;
                }

                $contentIds[] = $contentItem->getId();
            } 

            if ($contentItem instanceof DocumentInterface) {
                $contentIds[] = $contentItem->__get('type_id');
            }
        }

        $query = $this->createQueryBuilder()
            ->field('relations.references.$id')->in($contentIds)
            ->field('id')->notEqual($excludeContent->getId());

        if ($filterPublished) {
            $query->field('disabled')->equals(false)
                ->field('publishTime.startDate')->lte(new \DateTime())
                ->field('publishTime.endDate')->gte(new \DateTime());
        }

        if ($relation) {
            $query->field('relations.relationId')->equals($relation->getId());
        }

        return $query;
    }

    /**
     * Deletes all references to a content item.
     *
     * @param $id
     */
    public function deleteReference($id)
    {
        $documents = $this->findBy(['relations.references.$id' => $id]);

        /** @var Content $document */
        foreach ($documents as $document) {
            /** @var \Integrated\Bundle\ContentBundle\Document\Content\Embedded\Relation $relation */
            foreach ($document->getRelations() as $relation) {
                foreach ($relation->getReferences() as $reference) {
                    if ($reference->getId() == $id) {
                        $relation->removeReference($reference);
                    }
                }
            }
        }

        $this->dm->flush($documents);
    }
}
