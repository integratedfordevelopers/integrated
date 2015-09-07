<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\BlockBundle\Form\Type;

use Doctrine\MongoDB\ArrayIterator;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Integrated\Bundle\BlockBundle\Block\BundleChecker;

/**
 * @author Developer at optimum-web.com
 */
class BlockFilterType extends AbstractType
{
    /** @var array */
    private $typeChoices = [];
    /** @var array */
    private $channelChoices = [];
    /** @var BundleChecker */
    private $bundleChecker;

    /**
     * @param MetadataFactoryInterface $factory
     * @param DocumentManager $dm
     * @param BundleChecker $bundleChecker
     */
    public function __construct(MetadataFactoryInterface $factory, DocumentManager $dm, BundleChecker $bundleChecker)
    {
        $this->bundleChecker = $bundleChecker;
        $this->typeChoices = $dm->getRepository('IntegratedBlockBundle:Block\Block')->getTypesForFacetFilter($factory);

        if ($bundleChecker->checkPageBundle()) {
            /* count numb of blocks for each channel */
            $groupCountChannels = $dm->createQueryBuilder('IntegratedPageBundle:Page\Page')
                ->group(array('channel.$id' => 1), array('total' => 0, 'blocks' => []))
                ->reduce(
                    'function (curr, result ) {
                        var i;
                        for (i in curr.grids) {
                            var k;
                            for (k in curr.grids[i].items) {
                                var item = curr.grids[i].items[k];

                                if ("block" in item && result.blocks.indexOf(item.block.$id) < 0) {
                                    result.total += 1;
                                    result.blocks.push(item.block.$id);
                                }
                            }
                        }
                    }'
                )
                ->getQuery()
                ->execute();

            foreach ($groupCountChannels as $groupCountChannel) {
                $channelId = $groupCountChannel['channel.$id'];
                $this->channelChoices[$channelId] = $channelId . ' ' . $groupCountChannel['total'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder->add(
            'type',
            'choice',
            ['choices' => $this->typeChoices, 'expanded' => true, 'multiple' => true]
        );

        /* if IntegratedPageBundle is installed show channels */
        if ($this->bundleChecker->checkPageBundle()) {
            $builder->add(
                'channels',
                'choice',
                ['choices' => $this->channelChoices, 'expanded' => true, 'multiple' => true]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_filter';
    }
}
