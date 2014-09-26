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

use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowFormType extends AbstractType
{
    /**
   	 * {@inheritdoc}
   	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('comment', 'textarea', ['required' => false]);

		$builder->add('state', 'workflow_state', ['workflow' => $options['workflow']]);

		$builder->add('assigned', 'user_choice', ['empty_value' => 'Not Assigned', 'empty_data'  => null, 'required' => false]);
		$builder->add('deadline', 'integrated_datetime');
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setRequired([
			'workflow'
		]);

		$resolver->setAllowedTypes([
			'workflow' => ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']
		]);
	}

    /**
   	 * {@inheritdoc}
   	 */
	public function getName()
	{
		return 'integrated_workflow';
	}
}