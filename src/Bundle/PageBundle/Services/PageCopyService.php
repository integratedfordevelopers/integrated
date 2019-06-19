<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\MappingException as MappingExceptionAlias;
use Doctrine\ODM\MongoDB\MongoDBException as MongoDBExceptionAlias;
use Integrated\Bundle\BlockBundle\Document\Block\Block;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Item;
use Integrated\Bundle\PageBundle\Document\Page\Grid\ItemsInterface;
use Integrated\Bundle\PageBundle\Document\Page\Page;

class PageCopyService
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var RouteCache
     */
    private $routeCache;

    /**
     * @param DocumentManager $documentManager
     * @param RouteCache      $routeCache
     */
    public function __construct(DocumentManager $documentManager, RouteCache $routeCache)
    {
        $this->documentManager = $documentManager;
        $this->routeCache = $routeCache;
    }

    /**
     * @param array $data
     *
     * @throws MongoDBExceptionAlias
     * @throws MappingExceptionAlias
     */
    public function copyPages(string $channel, array $data)
    {
        $targetChannel = $this->documentManager->getRepository(Channel::class)->find($data['targetChannel']);
        if ($targetChannel === false) {
            throw new \Exception('Channel not found');
        }

        $result = $this->documentManager->getRepository(Page::class)->findBy(
            [
                'channel.$id' => $channel,
            ]
        );

        /** @var Page $page */
        foreach ($result as $page) {
            if (isset($data['pages']['page'.$page->getId()]['selected']) && $data['pages']['page'.$page->getId()]['selected'] === true) {
                $existingPage = $this->documentManager->getRepository(Page::class)->findOneBy(
                    [
                        'channel.$id' => $targetChannel->getId(),
                        'path' => $page->getPath(),
                    ]
                );

                if ($existingPage !== null) {
                    $this->documentManager->remove($existingPage);
                }

                $this->documentManager->detach($page);

                /** @var Page $copiedPage */
                $copiedPage = clone $page;
                $copiedPage->setCreatedAt(new \DateTime());
                $copiedPage->setChannel($targetChannel);

                foreach ($copiedPage->getGrids() as $key => $grid) {
                    $this->copyGridBlocks($grid, $data['pages']['page'.$page->getId()]['blocks']);
                }

                $this->documentManager->persist($copiedPage);
                $this->documentManager->flush();

                $this->routeCache->clear();
            }
        }
    }

    /**
     * @param ItemsInterface $grid
     * @param array          $data
     *
     * @throws \Exception
     */
    private function copyGridBlocks(ItemsInterface $grid, array $data)
    {
        $gridItems = $grid->getItems();
        foreach ($gridItems as $key => $item) {
            if (!$item instanceof Item) {
                continue;
            }

            $block = $item->getBlock();

            if ($block instanceof Block) {
                //copy block
                if (isset($data['block_'.$block->getId()]['operation']) && $data['block_'.$block->getId()]['operation'] == 'clone') {
                    $copiedBlock = clone $block;
                    $copiedBlock->setId($data['block_'.$block->getId()]['newBlockId']);
                    $copiedBlock->setCreatedAt(new \DateTime());

                    $this->documentManager->persist($copiedBlock);

                    $item->setBlock($copiedBlock);
                }
            }

            if ($item->getRow()) {
                foreach ($item->getRow()->getColumns() as $columnKey => $column) {
                    $this->copyGridBlocks($column, $data);
                }
            }
        }
    }
}
