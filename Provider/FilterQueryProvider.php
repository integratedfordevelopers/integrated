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
use Integrated\Bundle\BlockBundle\Document\Block\InlineTextBlock;

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
     * @var bool
     */
    private $pageBundleInstalled;

    /**
     * @param ManagerRegistry $mr
     * @param BlockUsageProvider $blockUsageProvider
     * @param array $bundles
     */
    public function __construct(ManagerRegistry $mr, BlockUsageProvider $blockUsageProvider, array $bundles)
    {
        $this->mr = $mr;
        $this->blockUsageProvider = $blockUsageProvider;
        $this->pageBundleInstalled = isset($bundles['IntegratedPageBundle']);
    }

    /**
     * @param array|null $data
     * @return \Doctrine\MongoDB\Query\Builder
     */
    public function getBlocksByChannelQueryBuilder($data)
    {
        $qb = $this->mr->getManager()->createQueryBuilder(Block::class);

        $type = isset($data['type']) ? array_filter($data['type']) : null;
        if ($type) {
            $qb->field('class')->in($data['type']);
        } else {
            $qb->field('class')->notEqual(InlineTextBlock::class);
        }

        if (isset($data['q'])) {
            $qb->field('title')->equals(new \MongoRegex('/' . $data['q'] . '/i'));
        }

        $channels = isset($data['channels']) ? array_filter($data['channels']) : null;
        if ($this->pageBundleInstalled && $channels) {
            $availableBlockIds = [];

            foreach ($channels as $channel) {
                $availableBlockIds = array_merge($availableBlockIds, $this->blockUsageProvider->getBlocksPerChannel($channel));
            }

            $qb->field('id')->in($availableBlockIds);
        }

        return $qb;
    }

    /**
     * @param array|null $data
     * @return array
     */
    public function getBlockIds($data)
    {
        $queryBuilder = $this->getBlocksByChannelQueryBuilder($data);

        $blocks = $queryBuilder->select('_id')
            ->hydrate(false)
            ->getQuery()
            ->getIterator()
            ->toArray();

        return array_keys($blocks);
    }
}
