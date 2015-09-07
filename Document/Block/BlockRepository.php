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
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Integrated\Bundle\BlockBundle\Block\BundleChecker;

/**
 * Class BlockRepository
 * @package Integrated\Bundle\BlockBundle\Document\Block
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

        if ($filterData['type']) {
            $qb->field('class')->in($filterData['type']);
        }

        if ($bundleChecker->checkPageBundle() && $filterData['channels']) {
            $pages = $this->dm->createQueryBuilder('IntegratedPageBundle:Page\Page')
                ->field('channel.$id')->in($filterData['channels'])
                ->getQuery()
                ->execute();

            $availableBlockIds = [];
            foreach ($pages as $page) {
                /** @var $page \Integrated\Bundle\PageBundle\Document\Page\Page */
                foreach ($page->getGrids() as $grid) {

                    foreach ($grid->getItems() as $item) {
                        $blockId = $item->getBlock()->getId();
                        $availableBlockIds[$blockId] = $blockId;
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
