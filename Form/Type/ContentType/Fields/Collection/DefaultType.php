<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\ContentType\Fields\Collection;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Integrated\Common\Form\Mapping\MetadataInterface;
use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentType\Field\Collection\DefaultTransformer;
use Integrated\Bundle\ContentBundle\Form\Type\ContentType\Fields\DefaultType as FieldType;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class DefaultType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MetadataInterface $metadata */
        $metadata = $options['metadata'];

        foreach ($metadata->getFields() as $field) {
            $builder->add($field->getName(), FieldType::class, [
                'label' => $field->hasOption('label') ? $field->getOption('label') : ucfirst($field->getName()),
                'field' => $field,
            ]);
        }

        $builder->addModelTransformer(new DefaultTransformer());
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
        return 'integrated_content_type_default_fields';
    }
}
