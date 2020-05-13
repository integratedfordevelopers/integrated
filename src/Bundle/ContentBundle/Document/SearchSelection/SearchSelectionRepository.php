<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\SearchSelection;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * Repository for SearchSelection.
 *
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class SearchSelectionRepository extends DocumentRepository
{
    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function findPublicByUserId($id)
    {
        $builder = $this->createQueryBuilder();

        $builder->addOr($builder->expr()->field('userId')->equals($id));
        $builder->addOr($builder->expr()->field('public')->equals(true));

        $builder->sort('title');

        return $builder->getQuery()->execute();
    }
}
