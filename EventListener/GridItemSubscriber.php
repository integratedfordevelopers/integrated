<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\MongoDB\Events;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PreUpdateEventArgs;
use Integrated\Bundle\BlockBundle\Document\Block\TextBlock;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Grid;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Item;
use Integrated\Bundle\PageBundle\Document\Page\Grid\ItemsInterface;
use Integrated\Bundle\PageBundle\Document\Page\Page;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class GridItemSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove => 'preRemove',
            Events::preUpdate => 'preUpdate',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $document = $args->getDocument();

        if (!$document instanceof Page) {
            return;
        }

        foreach ($this->findTextBlocks($args->getDocumentManager(), $document) as $block) {
            $args->getDocumentManager()->remove($block);
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $document = $args->getDocument();

        if (!$document instanceof Page) {
            return;
        }

        $oldBlocks = $this->findTextBlocks($args->getDocumentManager(), $document);

        $newBlocks = $this->getGridTextBlocks($document);

        foreach (array_diff($oldBlocks, $newBlocks) as $removedBlock) {
            $args->getDocumentManager()->remove($removedBlock);
        }
    }

    /**
     * @param DocumentManager $dm
     * @param Page $page
     * @return array|TextBlock[]
     */
    protected function findTextBlocks(DocumentManager $dm, Page $page)
    {
        return $dm->getRepository(TextBlock::class)->findBy(['parentPage' => $page]);
    }

    /**
     * @param Page $page
     * @return array
     */
    protected function getGridTextBlocks(Page $page)
    {
        $blocks = [];

        foreach ($page->getGrids() as $grid) {
            if ($grid instanceof Grid) {
                $blocks = array_merge($blocks, $this->getGridItemsTextBlocks($grid, $page));
            }
        }

        return $blocks;
    }

    /**
     * @param ItemsInterface $grid
     * @param Page $page
     * @return array
     */
    protected function getGridItemsTextBlocks(ItemsInterface $grid, Page $page)
    {
        $blocks = [];

        foreach ($grid->getItems() as $item) {
            if (!$item instanceof Item) {
                continue;
            }

            $block = $item->getBlock();

            if ($block instanceof TextBlock && $page == $block->getParentPage()) {
                $blocks[$block->getId()] = $block;
            }

            if ($item->getRow()) {
                foreach ($item->getRow()->getColumns() as $column) {
                    $blocks = array_merge($blocks, $this->getGridItemsTextBlocks($column, $page));
                }
            }
        }

        return $blocks;
    }
}
