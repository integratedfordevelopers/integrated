<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\ContentType;

use Integrated\Bundle\FormTypeBundle\Form\Type\BootstrapCollectionType;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\FieldsTransformer;
use Integrated\Common\Form\Mapping\MetadataInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class FieldsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MetadataInterface $metadata */
        $metadata = $options['metadata'];

        $builder->add(
            'default',
            Fields\Collection\DefaultType::class,
            [
                'label' => false,
                'metadata' => $metadata,
            ]
        );

        $builder->add(
            'custom',
            BootstrapCollectionType::class,
            [
                'label' => false,
                'entry_type' => Fields\CustomType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'add_button_text' => 'Add  custom field',
                'delete_button_text' => 'Delete field',
                'sub_widget_col' => 9,
                'button_col' => 3,
            ]
        );

        $builder->addModelTransformer(new FieldsTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['metadata']);
        $resolver->setAllowedTypes('metadata', 'Integrated\\Common\\Form\\Mapping\\MetadataInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type_fields';
    }
}
