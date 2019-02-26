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
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class BlockUsageProvider
{
    /**
     * @var ManagerRegistry
     */
    protected $mr;

    /**
     * @var array|null
     */
    protected $blockPages = null;

    /**
     * @var array|null
     */
    protected $channelBlocks = null;

    /**
     * @var array|null
     */
    protected $currentPage = null;

    /**
     * @var array|null
     */
    protected $currentChannel = null;

    /**
     * @var Channel[]
     */
    protected $channels = [];

    /**
     * @param ManagerRegistry $mr
     */
    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr;
    }

    /**
     * @param string|null $blockId
     *
     * @return array|null
     */
    public function getPagesPerBlock($blockId = null)
    {
        if (null === $this->blockPages) {
            //loads blockPages
            $this->convertPages();

            //to prevent doing same logic every time if there are no results
            if (null === $this->blockPages) {
                $this->blockPages = [];
            }
        }

        if (null !== $blockId) {
            return \array_key_exists($blockId, $this->blockPages) ? $this->blockPages[$blockId] : null;
        }

        return $this->blockPages;
    }

    /**
     * @param string|null $channelId
     *
     * @return array
     */
    public function getBlocksPerChannel($channelId = null)
    {
        if (null === $this->channelBlocks) {
            //loads channelBlocks
            $this->convertPages();

            //to prevent doing same logic every time if there are no results
            if (null === $this->channelBlocks) {
                $this->channelBlocks = [];
            }
        }

        if (null !== $channelId) {
            return \array_key_exists($channelId, $this->channelBlocks) ? $this->channelBlocks[$channelId] : [];
        }

        return $this->channelBlocks;
    }

    /**
     * @param string $id
     *
     * @return Channel|null
     */
    public function getChannel($id)
    {
        if (!\array_key_exists($id, $this->channels)) {
            $this->channels[$id] = $this->mr->getManager()->getRepository(Channel::class)->find($id);
        }

        return $this->channels[$id];
    }

    /**
     * iterate pages to filter blocks.
     */
    protected function convertPages()
    {
        $dm = $this->mr->getManager();

        $pages = $dm->createQueryBuilder(Page::class)
            ->hydrate(false)
            ->select(['title', 'channel', 'locked', 'grids'])
            ->getQuery()
            ->getIterator();

        foreach ($pages as $page) {
            if (!\array_key_exists('grids', $page)) {
                continue;
            }

            $this->currentPage = array_intersect_key($page, array_flip(['_id', 'title', 'locked', 'channel']));

            $this->currentChannel = \array_key_exists('channel', $page) ? $page['channel']['$id'] : null;

            foreach ($page['grids'] as $grid) {
                if (!\array_key_exists('items', $grid)) {
                    continue;
                }
                $this->filterItems($grid['items']);
            }
        }
    }

    /**
     * Recursive iteration items to register blocks per page and per channel.
     *
     * @param array $items
     */
    protected function filterItems($items)
    {
        foreach ($items as $item) {
            if (\array_key_exists('row', $item) && \array_key_exists('columns', $item['row'])) {
                foreach ($item['row']['columns'] as $column) {
                    if (\array_key_exists('items', $column)) {
                        $this->filterItems($column['items']);
                    }
                }
            } elseif (\array_key_exists('block', $item)) {
                $this->blockPages[$item['block']['$id']][$this->currentPage['_id']] = $this->currentPage;

                if ($this->currentChannel) {
                    $this->channelBlocks[$this->currentChannel][$item['block']['$id']] = $item['block']['$id'];
                }
            }
        }
    }
}
