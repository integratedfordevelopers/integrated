<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\Type;

use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class StateType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', 'text');
		$builder->add('publishable', 'checkbox', ['required' => false]);

		$builder->add('permissions', 'workflow_definition_permissions', ['required' => false]);

//		$builder->add('transitions', 'choice', ['required' => false]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'empty_data' => function(FormInterface $form) { return new State(); },
			'data_class' => 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State',
		));
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_workflow_definition_state';
	}
}