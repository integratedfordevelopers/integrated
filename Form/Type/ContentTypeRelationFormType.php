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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeRelationFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('type');

        $builder->add('contentTypes', DocumentType::class, [
            'class'       => 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\ContentType',
            'property'    => 'name',
            'expanded'    => true,
            'multiple'    => true,
            'placeholder' => false
        ]);

        $builder->add('multiple', ChoiceType::class, [
            'choices'     => ['One', 'Multiple'],
            'expanded'    => true,
            'placeholder' => false
        ]);

        $builder->add('required', CheckboxType::class, ['required' => false]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\Embedded\\Relation',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type_relation';
    }
}
