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

use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Entity\Workflow\State;
use Integrated\Bundle\WorkflowBundle\Form\EventListener\CurrentStateListener;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowType extends AbstractType
{
	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('comment', 'text');

		$builder->add('state', 'text', ['read_only' => true]);

		$builder->add('action', 'workflow_actions', ['workflow' => $options['workflow']]);

		$builder->add('assigned', 'user_choice');
		$builder->add('deadline', 'datetime');

		$builder->addEventSubscriber(new CurrentStateListener());
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'empty_data' => function(FormInterface $form) { return new State(); },
			'data_class' => 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Workflow\\State',
		));

		$resolver->setRequired([
			'workflow'
		]);

		$resolver->setAllowedTypes([
			'workflow' => ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']
		]);
	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_workflow';
	}
}