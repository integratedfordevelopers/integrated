<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Bulk;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @author Patrick Mestebeld <patrick@e-active.nl>
 */
class BulkActionRepository extends DocumentRepository
{
    /**
     * @param $id
     *
     * @return BulkAction
     */
    public function findOneByIdAndNotExecuted($id)
    {
        return $this->createQueryBuilder()
            ->field('id')->equals($id)
            ->field('executedAt')->equals(null)
            ->getQuery()
            ->getSingleResult();
    }
}
