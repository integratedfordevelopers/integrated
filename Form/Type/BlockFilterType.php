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

use Integrated\Bundle\BlockBundle\Provider\BlockUsageProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ODM\MongoDB\DocumentManager;

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
     * @var BlockUsageProvider
     */
    private $blockUsageProvider;

    /**
     * @var bool
     */
    private $pageBundleInstalled;

    /**
     * @param MetadataFactoryInterface $factory
     * @param DocumentManager          $dm
     * @param BlockUsageProvider       $blockUsageProvider
     * @param array                    $bundles
     */
    public function __construct(
        MetadataFactoryInterface $factory,
        DocumentManager $dm,
        BlockUsageProvider $blockUsageProvider,
        array $bundles
    ) {
        $this->factory = $factory;
        $this->dm = $dm;
        $this->blockUsageProvider = $blockUsageProvider;
        $this->pageBundleInstalled = isset($bundles['IntegratedPageBundle']);
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
            ['choices' => $this->getTypeChoices($options['blockIds']), 'expanded' => true, 'multiple' => true]
        );

        /* if IntegratedPageBundle is installed show channels */
        if ($this->pageBundleInstalled) {
            $builder->add(
                'channels',
                'choice',
                ['choices' => $this->getChannelChoices($options['blockIds']), 'expanded' => true, 'multiple' => true]
            );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('blockIds');
        $resolver->setAllowedTypes('blockIds', 'array');
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_filter';
    }

    /**
     * @param array $blockIds
     * @return mixed
     */
    private function getTypeChoices(array $blockIds)
    {
        return $this->dm->getRepository('IntegratedBlockBundle:Block\Block')->getTypeChoices(
            $this->factory,
            $blockIds
        );
    }

    /**
     * @param array $blockIds
     * @return array
     */
    private function getChannelChoices(array $blockIds)
    {
        $channels = $this->blockUsageProvider->getBlocksPerChannel();

        $channelChoices = [];
        foreach ($channels as $channelId => $blocks) {
            $count = count(array_intersect($blocks, $blockIds));
            if ($count) {
                $channelChoices[$channelId] = $this->blockUsageProvider->getChannel($channelId)->getName() . ' ' . $count;
            }
        }

        return $channelChoices;
    }
}
