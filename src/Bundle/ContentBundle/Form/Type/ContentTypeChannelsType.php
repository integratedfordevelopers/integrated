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

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ChannelsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentTypeChannelsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('options', ChoiceType::class, [
            'choices' => [
                'Enable channel field' => '',
                'Enable but hide channel field' => 'hidden',
                'Disable channel field' => 'disabled',
            ],
            'choices_as_values' => true,
            'required' => false,
        ]);

        $builder->add(
            'defaults',
            ContentTypeChannelCollectionType::class,
            ['label' => 'Channels', 'required' => false]
        );

        $builder->addViewTransformer(new ChannelsTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type_channels';
    }
}
