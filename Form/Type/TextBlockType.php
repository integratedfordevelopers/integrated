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

use Integrated\Bundle\BlockBundle\Document\Block\TextBlock;
use Integrated\Bundle\FormTypeBundle\Form\Type\EditorType;
use Integrated\Bundle\FormTypeBundle\Form\Type\SaveCancelType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class TextBlockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');

        $builder->add('content', EditorType::class, [
            'mode' => 'web'
        ]);

        $builder->add('actions', SaveCancelType::class, ['cancel_route' => 'integrated_block_block_index']);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', TextBlock::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_text_block';
    }
}
