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
     * @param array $data
     * @param bool $pageBundleInstalled
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getBlocksByChannelQueryBuilder(array $data, $pageBundleInstalled = false)
    {
        $qb = $this->mr->getManager()->createQueryBuilder(Block::class);

        if (isset($data['type'])) {
            $qb->field('class')->in($data['type']);
        }

        if (isset($data['type'])) {
            $qb->field('title')->equals(new \MongoRegex('/' . $data['q'] . '/i'));
        }

        if ($pageBundleInstalled && isset($data['channels'])) {
            $availableBlockIds = [];

            foreach ($data['channels'] as $channel) {
                $availableBlockIds = array_merge($availableBlockIds, $this->blockUsageProvider->getBlocksPerChannel($channel));
            }

            $qb->field('id')->in($availableBlockIds);
        }

        return $qb;
    }
}
