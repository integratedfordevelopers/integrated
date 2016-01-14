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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 */
class BlockSizeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('block', 'integrated_block');

        $builder->add('size_xs', 'integer', [
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);

        $builder->add('size_sm', 'integer', [
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);

        $builder->add('size_md', 'integer', [
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);

        $builder->add('size_lg', 'integer', [
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\Bundle\BlockBundle\Document\Block\Embedded\BlockSize',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_block_size';
    }
}
