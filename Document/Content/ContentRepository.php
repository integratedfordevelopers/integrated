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
     * @param Content $content
     * @param Relation $relation
     * @return array
     */
    public function getCurrentDocumentLinked(Content $content, Relation $relation)
    {
        return $this->createQueryBuilder()
            ->field('relations.relationId')->equals($relation->getId())
            ->field('relations.references.$id')->equals($content->getId());
    }
}