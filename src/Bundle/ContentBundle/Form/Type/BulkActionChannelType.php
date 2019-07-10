<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type;

use Integrated\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Integrated\Bundle\ContentBundle\Document\Bulk\Action\ChannelAction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BulkActionChannelType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'channel',
            ChannelChoiceType::class,
            [
                'label' => $options['label'],
                'multiple' => false,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['channel_handler'])
            ->setAllowedTypes('channel_handler', 'string')
            ->setDefault('data_class', ChannelAction::class)
            ->setDefault('empty_data', function (Options $options) {
                $action = new ChannelAction($options['channel_handler']);

                return $action;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_bulk_action_channel';
    }
}
