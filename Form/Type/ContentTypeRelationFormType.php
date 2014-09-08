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
class ContentTypeRelationFormType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('type');

        $builder->add('contentTypes', 'document',[
			'class'       => 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\ContentType',
			'property'    => 'name',
			'expanded'    => true,
			'multiple'    => true,
			'empty_value' => false
		]);

        $builder->add('multiple', 'choice',[
			'choices'     => ['One', 'Multiple'],
			'expanded'    => true,
			'empty_value' => false
		]);

        $builder->add('required', 'checkbox', ['required' => false]);
    }

	/**
	 * @inheritdoc
	 */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Integrated\\Bundle\\ContentBundle\\Document\\ContentType\\Embedded\\Relation',
        ]);
    }

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_content_type_relation';
	}
}