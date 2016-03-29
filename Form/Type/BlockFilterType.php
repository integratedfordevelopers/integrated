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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class BlockFilterType extends AbstractType
{
    /**
     * @var MetadataFactoryInterface $factory
     */
    private $factory;

    /**
     * @var DocumentManager
     */
    private $dm;

    /**
     * @var bool
     */
    private $pageBundleInstalled;

    /**
     * @param MetadataFactoryInterface $factory
     * @param DocumentManager          $dm
     * @param ContainerInterface       $serviceContainer
     */
    public function __construct(MetadataFactoryInterface $factory, DocumentManager $dm, ContainerInterface $serviceContainer)
    {
        $this->factory = $factory;
        $this->dm = $dm;
        $this->pageBundleInstalled = $serviceContainer->has('integrated_page.form.type.page');
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
            ['choices' => $this->getTypeChoices(), 'expanded' => true, 'multiple' => true]
        );

        /* if IntegratedPageBundle is installed show channels */
        if ($this->pageBundleInstalled) {
            $builder->add(
                'channels',
                'choice',
                ['choices' => $this->getChannelChoices(), 'expanded' => true, 'multiple' => true]
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

    /**
     * @return mixed
     */
    private function getTypeChoices()
    {
        return $this->dm->getRepository('IntegratedBlockBundle:Block\Block')->getTypeChoices($this->factory);
    }

    /**
     * @return array
     */
    private function getChannelChoices()
    {
        /* count numb of blocks for each channel */
        $groupCountChannels = $this->dm->createQueryBuilder('IntegratedPageBundle:Page\Page')
            ->group(array('channel.$id' => 1), array('total' => 0, 'blocks' => []))
            ->reduce(
                'function (curr, result) {
                        var checkItem = function(item) {
                            if ("block" in item && result.blocks.indexOf(item.block.$id) < 0) {
                                result.total += 1;
                                result.blocks.push(item.block.$id);
                            }

                            if ("row" in item) {
                                recursiveFindInRows(item.row);
                            }
                        }

                        var recursiveFindInRows = function(row) {
                            if ("columns" in row) {
                                for (var c in row.columns) {
                                    if ("items" in row.columns[c]) {
                                        for (var i in row.columns[c].items) {
                                            checkItem(row.columns[c].items[i]);
                                        }
                                    }
                                }
                            }
                        };

                        for (i in curr.grids) {
                            for (k in curr.grids[i].items) {
                                checkItem(curr.grids[i].items[k]);
                            }
                        }
                    }'
            )
            ->getQuery()
            ->execute();

        $channelChoices = [];
        foreach ($groupCountChannels as $groupCountChannel) {
            if ($groupCountChannel['total'] > 0) {
                $channelId = $groupCountChannel['channel.$id'];
                $channelChoices[$channelId] = $channelId.' '.$groupCountChannel['total'];
            }
        }

        return $channelChoices;
    }
}
