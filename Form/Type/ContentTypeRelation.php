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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeRelation extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name'
        );

        $builder->add(
            'type'
        );

        $builder->add(
            'contentTypes',
            'document',
            array(
                'class' => 'Integrated\Bundle\ContentBundle\Document\ContentType\ContentType',
                'property' => 'name',
                'expanded' => true,
                'multiple' => true,
                'empty_value' => false
            )
        );

        $builder->add(
            'multiple',
            'choice',
            array(
                'choices' => array(
                    0 => 'One',
                    1 => 'Multiple'
                ),
                'expanded' => true,
                'empty_value' => false
            )
        );

        $builder->add(
            'required',
            'checkbox',
            array(
                'required' => false
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'content_type_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Integrated\Bundle\ContentBundle\Document\ContentType\Embedded\Relation',
        ));
    }
}