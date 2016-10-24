<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Provider;

use Doctrine\Common\Persistence\ManagerRegistry;

use Integrated\Bundle\BlockBundle\Document\Block\Block;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class FilterQueryProvider
{
    /**
    * @var ManagerRegistry
    */
    protected $mr;

    /**
     * @var BlockUsageProvider
     */
    protected $blockUsageProvider;

    /**
     * @param ManagerRegistry $mr
     */
    public function __construct(ManagerRegistry $mr, BlockUsageProvider $blockUsageProvider)
    {
        $this->mr = $mr;
        $this->blockUsageProvider = $blockUsageProvider;
    }

    /**
     * @param array $type
     * @param array $channels
     * @param bool $pageBundleInstalled
     * @param string|null $query
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getBlocksByChannelQueryBuilder(array $type = [], array $channels = [], $pageBundleInstalled = false, $query = null)
    {
        $qb = $this->mr->getManager()->createQueryBuilder(Block::class);

        if ($type) {
            $qb->field('class')->in($type);
        }

        if ($query) {
            $qb->field('title')->equals(new \MongoRegex('/' . $query . '/i'));
        }

        if ($pageBundleInstalled && $channels) {
            $availableBlockIds = [];

            foreach ($channels as $channel) {
                $availableBlockIds = array_merge($availableBlockIds, $this->blockUsageProvider->getBlocksPerChannel($channel));
            }

            $qb->field('id')->in($availableBlockIds);
        }

        return $qb;
    }
}
