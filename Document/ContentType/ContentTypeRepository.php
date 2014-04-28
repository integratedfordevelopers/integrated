<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\ContentType;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation;
use Integrated\Common\ContentType\ContentTypeInterface;
use Integrated\Common\ContentType\ContentTypeRelationInterface;

/**
 * Repository for ContentType
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeRepository extends DocumentRepository
{
    /**
     * @param $type
     * @param string $target
     * @param string $parent
     * @param bool $createNew
     * @return bool|ContentTypeRelationInterface
     */
    public function findRelationByType($type, $target, $parent, $createNew = true)
    {
        $relation = false;

        /** @var ContentTypeInterface $parent */
        if (!$parent = $this->findOneBy(array('type' => $parent))) {
            return false;
        }

        /** @var ContentTypeInterface $target */
        if (!$target = $this->findOneBy(array('type' => $target))) {
            return false;
        }

        /** @var $relation ContentTypeRelationInterface */
        foreach ($parent->getRelations() as $contentTypeRelation) {
            if ($contentTypeRelation->getType() == $type) {
                foreach ($contentTypeRelation->getContentTypes() as $contentType) {
                    if ($contentType->getType() == $target->getType()) {
                        $relation = $contentTypeRelation;
                        break;
                    }
                }
            }
        }

        if (false === $relation) {
            if (true === $createNew) {
                $relation = new Relation();
                $relation->setType($type)
                    ->setName($type)
                    ->setMultiple(true)
                    ->setContentTypes(new ArrayCollection(array($target)));

                $parent->addRelation($relation);
                $this->getDocumentManager()->persist($parent);
                $this->getDocumentManager()->flush($parent);
            }
        }

        return $relation;
    }
}