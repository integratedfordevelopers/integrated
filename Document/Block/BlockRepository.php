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
use Integrated\Bundle\BlockBundle\Utils\BundleChecker;

/**
 * Class BlockRepository
 * @package Integrated\Bundle\BlockBundle\Document\Block
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class BlockRepository extends DocumentRepository
{
    /**
     * @param array $filterData
     * @param BundleChecker $bundleChecker
     * @return mixed
     */
    public function getQBForBlockPaginator(array $filterData, BundleChecker $bundleChecker)
    {
        $qb = $this->createQueryBuilder();

        if (isset($filterData['type']) && $filterData['type']) {
            $qb->field('class')->in($filterData['type']);
        }

        if ($bundleChecker->checkPageBundle() && isset($filterData['channels']) && $filterData['channels']) {
            $pages = $this->dm->createQueryBuilder('IntegratedPageBundle:Page\Page')
                ->field('channel.$id')->in($filterData['channels'])
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
    public function getTypesForFacetFilter(MetadataFactoryInterface $factory)
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
                $typeChoices[$class] = $metaData->getType() . ' ' . $typeCount[$class];
            }
        }

        return $typeChoices;
    }
}
