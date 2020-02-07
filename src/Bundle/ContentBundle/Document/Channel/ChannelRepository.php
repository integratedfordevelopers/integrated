<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Channel;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ChannelRepository extends DocumentRepository
{
    /**
     * @param array $ids
     *
     * @return Channel[]
     */
    public function findByIds(array $ids)
    {
        $qb = $this->createQueryBuilder();
        $qb->field('id')->in($ids);

        return $qb->getQuery()->getIterator()->toArray();
    }
}
