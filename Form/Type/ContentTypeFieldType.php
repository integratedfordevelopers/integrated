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

use Integrated\Bundle\ContentBundle\Form\DataTransformer\ContentTypeField as ContentTypeFieldTransformer;

use Integrated\Common\Form\Mapping\AttributeInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 */
class ContentTypeFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AttributeInterface $field */
        $field = $options['field'];

        $builder->add('enabled', 'checkbox', [
            'required' => false,
            'label'    => $field->hasOption('label') ? $field->getOption('label') : ucfirst($field->getName()),
        ]);

        $builder->add('required', 'checkbox', ['required' => false]);

        $builder->addModelTransformer(new ContentTypeFieldTransformer($field));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['field']);
        $resolver->setAllowedTypes(['field' => 'Integrated\\Common\\Form\\Mapping\\AttributeInterface']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'integrated_content_type_field';
    }
}
