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

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Integrated\Bundle\PageBundle\Document\Page\Page;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class BlockRepository extends DocumentRepository
{
    /**
     * @param MetadataFactoryInterface $factory
     * @param array|null               $ids
     *
     * @return array
     */
    public function getTypeChoices(MetadataFactoryInterface $factory, array $ids = null)
    {
        $qb = $this->createAggregationBuilder();

        if (null !== $ids) {
            $qb->match()->field('_id')->in($ids);
        }

        $qb->group()
            ->field('_id')
            ->expression('$class')
            ->field('total')
            ->sum(1);

        $groupCountBlock = $qb->getAggregation();

        $typeCount = [];
        foreach ($groupCountBlock as $result) {
            $typeCount[$result['_id']] = $result['total'];
        }

        $typeChoices = [];
        foreach ($factory->getAllMetadata() as $metaData) {
            $class = $metaData->getClass();

            if (\array_key_exists($class, $typeCount) && $typeCount[$class]) {
                $typeChoices[$metaData->getType().' ('.$typeCount[$class].')'] = $class;
            }
        }

        ksort($typeChoices);

        return $typeChoices;
    }

    /**
     * @param Block $block
     *
     * @return \Doctrine\ODM\MongoDB\Query\Query
     *
     * @internal heavy query, multiple calls make page slow
     */
    public function pagesByBlockQb(Block $block)
    {
        return $this->dm
            ->createQueryBuilder(Page::class)
            ->where('function() {
                var block_id = "'.$block->getId().'";

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
                            for (var c in row.columns) {
                                if ("items" in row.columns[c]) {
                                    for (var i in row.columns[c].items) {
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
     * Check if given block is used on some page.
     *
     * @param Block $block
     *
     * @return bool
     *
     * @internal heavy query, multiple calls make page slow
     */
    public function isUsed(Block $block)
    {
        return $this->pagesByBlockQb($block)->getSingleResult() ? true : false;
    }
}
