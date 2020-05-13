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

use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class RelationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type')
            ->add(
                'sources',
                DocumentType::class,
                [
                    'class' => ContentType::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'required' => false,
                ]
            )->add(
                'targets',
                DocumentType::class,
                [
                        'class' => ContentType::class,
                        'choice_label' => 'name',
                        'multiple' => true,
                        'required' => false,
                        ]
            )->add(
                'multiple',
                null,
                [
                            'required' => false,
                            'attr' => [
                            'align_with_widget' => true,
                            ],
                            ]
            )
            ->add(
                'required',
                null,
                [
                    'required' => false,
                    'attr' => [
                        'align_with_widget' => true,
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\Relation\\Relation',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_relation';
    }
}
