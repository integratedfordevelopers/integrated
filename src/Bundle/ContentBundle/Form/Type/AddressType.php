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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jurre de Jongh <jurre@e-active.nl>
 */
class AddressType extends AbstractType
{
    /**
     * @const array
     */
    public const PROPERTIES = ['type', 'name', 'country', 'address1', 'address2', 'zipcode', 'city'];

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['fields'] as $field) {
            // Variables
            $type = TextType::class;
            $default = ['required' => $builder->getRequired()];
            $override = isset($options['options'][$field]) ? $options['options'][$field] : [];

            // Spec may vary per field, but not per se
            switch ($field) {
                case 'type':
                    $type = ChoiceType::class;
                    $default = [
                        'placeholder' => '',
                        'required' => false,
                        'choices' => [
                            'postal' => 'Postal address',
                            'visiting' => 'Visiting address',
                            'mailing' => 'Mailing address',
                        ],
                    ];
                    break;
                case 'country':
                    $type = CountryType::class;
                    $default['placeholder'] = '';
                    break;
            }

            // Add into the form
            $builder->add($field, $type, array_merge($default, $override));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Set defaults for the resolver
        $resolver->setDefaults([
            'data_class' => Address::class,
            'options' => [],
            'fields' => self::PROPERTIES, // @todo validate options (INTEGRATED-627)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_address';
    }
}
