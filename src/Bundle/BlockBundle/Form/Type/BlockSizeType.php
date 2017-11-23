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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
        $builder->add('block', BlockType::class);

        $builder->add('size_xs', IntegerType::class, [
            'label' => 'Extra small devices',
            'attr' => [
                'help_text' => 'Any screen less than 768px wide (most likely phones).',
            ],
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);

        $builder->add('size_sm', IntegerType::class, [
            'label' => 'Small devices',
            'attr' => [
                'help_text' => 'Any screen bigger (or equal) than 768px wide (most likely tablets).',
            ],
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);

        $builder->add('size_md', IntegerType::class, [
            'label' => 'Medium devices',
            'attr' => [
                'help_text' => 'Any screen bigger (or equal) than 992px wide (most likely desktops).',
            ],
            'required' => false,
            'constraints' => new Range([
                'min' => 1,
                'max' => 12,
            ]),
        ]);

        $builder->add('size_lg', IntegerType::class, [
            'label' => 'Large devices',
            'attr' => [
                'help_text' => 'Any screen bigger (or equal) than 1200px wide (full hd and bigger screens).',
            ],
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
    public function getBlockPrefix()
    {
        return 'integrated_block_size';
    }
}
