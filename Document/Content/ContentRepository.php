<?php
namespace Integrated\Bundle\ContentBundle\Document\Content;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\Relation\Relation;

/**
 * Class ContentRepository
 *
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class ContentRepository extends DocumentRepository
{
    /**
     * Get items which have the current document linked
     *
     * @param Content       $content
     * @param Relation|null $relation
     * @param Content|null  $excludeContent
     * @param bool $filterPublished
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getUsedBy(Content $content, Relation $relation = null, Content $excludeContent = null, $filterPublished = true)
    {
        if (!$excludeContent) {
            $excludeContent = $content;
        }
        $query = $this->createQueryBuilder()
            ->field('relations.references.$id')->equals($content->getId())
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
     * Deletes all references to a content item
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