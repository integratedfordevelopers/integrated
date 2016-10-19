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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Doctrine\ODM\MongoDB\DocumentManager;

use Integrated\Bundle\BlockBundle\Util\PageUsageUtil;
use Integrated\Common\Form\Mapping\MetadataFactoryInterface;

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
     * @var PageUsageUtil
     */
    private $pageUsageUtil;

    /**
     * @var bool
     */
    private $pageBundleInstalled;

    /**
     * @param MetadataFactoryInterface $factory
     * @param DocumentManager          $dm
     * @param PageUsageUtil            $pageUsageUtil
     */
    public function __construct(MetadataFactoryInterface $factory, DocumentManager $dm, PageUsageUtil $pageUsageUtil)
    {
        $this->factory = $factory;
        $this->dm = $dm;
        $this->pageUsageUtil = $pageUsageUtil;
        $this->pageBundleInstalled = class_exists('\Integrated\Bundle\PageBundle\IntegratedPageBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setMethod('GET');

        $builder->add(
            'q',
            'text',
            ['attr' => ['placeholder' => 'Filter block name']]
        );

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
     * @return array
     */
    private function getTypeChoices()
    {
        return $this->dm->getRepository('IntegratedBlockBundle:Block\Block')->getTypeChoices(
            $this->factory,
            $this->pageUsageUtil->getFilteredBlockIds()
        );
    }

    /**
     * @return array
     */
    private function getChannelChoices()
    {
        $channels = $this->pageUsageUtil->getBlocksPerChannel();

        $channelChoices = [];
        foreach ($channels as $channelId => $blocks) {
            $count = count(array_intersect($blocks, $this->pageUsageUtil->getFilteredBlockIds()));
            if ($count) {
                $channelChoices[$channelId] = $this->pageUsageUtil->getChannel($channelId)->getName() . ' ' . $count;
            }
        }

        return $channelChoices;
    }
}
