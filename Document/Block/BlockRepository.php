<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Document\Block;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Column;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Item;
use Integrated\Bundle\PageBundle\Document\Page\Grid\Row;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class BlockRepository extends DocumentRepository
{
    /**
     * @param $type
     * @param $channels
     * @param $pageBundleInstalled
     * @return mixed
     */
    public function getBlocksByChannelQueryBuilder($type, $channels, $pageBundleInstalled)
    {
        $qb = $this->createQueryBuilder();

        if ($type) {
            $qb->field('class')->in($type);
        }

        if ($pageBundleInstalled && $channels) {
            $pages = $this->dm->createQueryBuilder('IntegratedPageBundle:Page\Page')
                ->field('channel.$id')->in($channels)
                ->getQuery()
                ->execute();

            $availableBlockIds = [];
            $recursiveFindInRow = function (Row $row) use (&$recursiveFindInRow, &$availableBlockIds) {
                foreach ($row->getColumns() as $column) {
                    /** @var $column Column */
                    foreach ($column->getItems() as $item) {
                        if ($block = $item->getBlock()) {
                            $availableBlockIds[$block->getId()] = $block->getId();
                        }

                        if ($row = $item->getRow()) {
                            $recursiveFindInRow($row);
                        }
                    }
                }
            };

            foreach ($pages as $page) {
                /** @var $page \Integrated\Bundle\PageBundle\Document\Page\Page */
                foreach ($page->getGrids() as $grid) {
                    foreach ($grid->getItems() as $item) {
                        if ($block = $item->getBlock()) {
                            $availableBlockIds[$block->getId()] = $block->getId();
                        }

                        if ($row = $item->getRow()) {
                            $recursiveFindInRow($row);
                        }
                    }
                }
            }

            $qb->field('id')->in($availableBlockIds);
        }

        return $qb;
    }


    /**
     * @param MetadataFactoryInterface $factory
     * @return array
     */
    public function getTypeChoices(MetadataFactoryInterface $factory)
    {
        $groupCountBlock = $this->createQueryBuilder()
            ->group(array('class' => 1), array('total' => 0))
            ->reduce('function (curr, result ) { result.total += 1;}')
            ->getQuery()
            ->execute();

        $typeCount = [];
        foreach ($groupCountBlock as $result) {
            $typeCount[$result['class']] = $result['total'];
        }

        $typeChoices = [];
        foreach ($factory->getAllMetadata() as $metaData) {
            /** @var $metaData \Integrated\Common\Form\Mapping\Metadata\Document */
            $class = $metaData->getClass();

            if (array_key_exists($class, $typeCount)) {
                $typeChoices[$class] = $metaData->getType().' '.$typeCount[$class];
            }
        }

        return $typeChoices;
    }


    /**
     * @param Block $block
     * @return \Doctrine\MongoDB\Query\Query
     */
    public function pagesByBlockQb(Block $block)
    {
        return $this->dm
            ->getRepository('IntegratedPageBundle:Page\Page')
            ->createQueryBuilder()
            ->where('function() {
                var block_id = "' . $block->getId() . '";

                var checkItem = function(item) {
                        if ("block" in item && item.block.$id == block_id) {
                            return true;
                        }

                        if ("row" in item) {
                            if (recursiveFindInRows(item.row)) {
                                return true;
                            }
                        }
                    }

                    var recursiveFindInRows = function(row) {
                        if ("columns" in row) {
                            for (c in row.columns) {
                                if ("items" in row.columns[c]) {
                                    for (i in row.columns[c].items) {

                                        if (checkItem(row.columns[c].items[i])) {
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
                    };

                    for (k in this.grids) {
                        for (i in this.grids[k].items) {

                            if (checkItem(this.grids[k].items[i])) {
                                return true;
                            }
                        }
                    }

                    return false;

            }')
            ->getQuery();
    }


    /**
     * Check if given block is used on some page
     *
     * @param Block $block
     * @return bool
     */
    public function isUsed(Block $block)
    {
        return $this->pagesByBlockQb($block)->getSingleResult() ? true : false;
    }
}
