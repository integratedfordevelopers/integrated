<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Form\Type\ContentType\Fields;

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeField as ContentTypeFieldTransformer;
use Integrated\Common\Form\Mapping\AttributeInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        /** @var AttributeInterface $field */
        $field = $options['field'];

        $builder->add('enabled', CheckboxType::class, [
            'required' => false,
            'label'    => $field->hasOption('label') ? $field->getOption('label') : ucfirst($field->getName()),
        ]);

        $builder->add('required', CheckboxType::class, ['required' => false]);

        $builder->addModelTransformer(new ContentTypeFieldTransformer($field));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['field']);
        $resolver->setAllowedTypes('field', 'Integrated\\Common\\Form\\Mapping\\AttributeInterface');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'integrated_content_type_field';
    }
}
