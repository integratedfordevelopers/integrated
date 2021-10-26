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

use Doctrine\Persistence\ManagerRegistry;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\BlockBundle\Document\Block\InlineTextBlock;
use Integrated\Bundle\UserBundle\Model\UserInterface;

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
     * @param ManagerRegistry    $mr
     * @param BlockUsageProvider $blockUsageProvider
     * @param array              $bundles
     */
    public function __construct(ManagerRegistry $mr, BlockUsageProvider $blockUsageProvider, array $bundles)
    {
        $this->mr = $mr;
        $this->blockUsageProvider = $blockUsageProvider;
        $this->pageBundleInstalled = isset($bundles['IntegratedPageBundle']);
    }

    /**
     * @param array|null  $data
     * @param object|null $groupUser
     *
     * @return \Doctrine\MongoDB\Query\Builder
     *
     * @throws \MongoException
     */
    public function getBlocksByChannelQueryBuilder($data, ?object $groupUser)
    {
        $qb = $this->mr->getManager()->createQueryBuilder(Block::class);

        $type = isset($data['type']) ? array_filter($data['type']) : null;
        if ($type) {
            $qb->field('class')->in($data['type']);
        } else {
            $qb->field('class')->notEqual(InlineTextBlock::class);
        }

        if (isset($data['q'])) {
            $qb->field('title')->equals(new \MongoRegex('/'.$data['q'].'/i'));
        }

        $channels = isset($data['channels']) ? array_filter($data['channels']) : null;
        if ($this->pageBundleInstalled && $channels) {
            $availableBlockIds = [];

            foreach ($channels as $channel) {
                $availableBlockIds = array_merge($availableBlockIds, $this->blockUsageProvider->getBlocksPerChannel($channel));
            }

            $qb->field('id')->in($availableBlockIds);
        }

        if ($groupUser !== null) {
            $qb->field('groups')->in($this->getUserGroupIds($groupUser));
        }

        return $qb;
    }

    /**
     * @param array|null  $data
     * @param object|null $groupUser
     *
     * @return array
     *
     * @throws \MongoException
     */
    public function getBlockIds($data, ?object $groupUser)
    {
        $queryBuilder = $this->getBlocksByChannelQueryBuilder($data, $groupUser);

        $blocks = $queryBuilder->select('_id')
            ->hydrate(false)
            ->getQuery()
            ->getIterator()
            ->toArray();

        return array_keys($blocks);
    }

    /**
     * @param object $user
     *
     * @return array
     */
    private function getUserGroupIds($user)
    {
        $groupIds = [];
        if ($user instanceof UserInterface) {
            foreach ($user->getGroups() as $group) {
                $groupIds[] = $group->getId();
            }
        }

        return $groupIds;
    }
}
