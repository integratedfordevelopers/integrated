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

use Integrated\Bundle\WorkflowBundle\Form\EventListener\WorkflowStateListener;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowStateType extends AbstractType
{
//	/**
//	 * @var ObjectRepository
//	 */
//	private $repository;
//
//	/**
//	 * @param ObjectRepository $repository
//	 */
//	public function __construct(ObjectRepository $repository)
//	{
//		$this->repository = $repository;
//	}

	/**
	 * @inheritdoc
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('current', 'text', [
			'read_only' => true,
			'mapped' => false,
			'label' => 'State'
		]);

		$builder->add('next', 'workflow_state_choice', [
			'workflow' => $options['workflow'],
			'mapped' => false,
			'label' => 'Next state'
		]);

		$builder->addEventSubscriber(new WorkflowStateListener());
	}

	public function finishView(FormView $view, FormInterface $form, array $options)
	{
		// the current field is just a text field but to give it the possibility
		// to style is differently a new block prefix will be added just before
		// the last one. So one can use the integrated_workflow_state_text_widget
		// or workflow_state_text_widget block to style this text differently from
		// other text fields.

		$child = $view->children['current'];

		$last = array_pop($child->vars['block_prefixes']);

		$child->vars['block_prefixes'][] = 'workflow_state_text';
		$child->vars['block_prefixes'][] = 'integrated_workflow_state_text';
		$child->vars['block_prefixes'][] = $last;

		// filter out the current field if there is no current state is set

		if (!$child->vars['value']) {
			unset($view->children['current']);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults([
			'empty_data' => null,
			'data_class' => 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State',
		]);

		$resolver->setRequired([
			'workflow'
		]);

		$resolver->setAllowedTypes([
			'workflow' => ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']
		]);
	}

//	/**
//	 * @inheritdoc
//	 */
//	public function setDefaultOptions(OptionsResolverInterface $resolver)
//	{
//		$choiceList = function(Options $options) {
//			$states = $options['workflow']->getStates();
//
//			if ($options['choice_current']) {
//				// filter out the current selection as that is going to be the default value
//				// and should not be in the selection list.
//				foreach ($states as $key => $value) {
//					if ($value === $options['choice_current']) { unset($states[$key]); }
//				}
//			}
//
//			return new ObjectChoiceList($states, 'name', [], null, 'id');
//		};
//
//		$emptyDataNormalizer = function(Options $options) {
//			if ($options['choice_current']) {
//				return $options['choice_current'];
//			}
//
//			return null;
//		};
//
//		$workflowNormalizer = function(Options $options, $workflow) {
//			if (is_string($workflow)) {
//				$workflow = $this->repository->find($workflow);
//			}
//
//			if (!$workflow instanceof Definition) {
//				throw new InvalidOptionsException(sprintf(
//					'The option "%s" could not be normalized to a valid "%s" object',
//					'workflow',
//					'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition'
//				));
//			}
//
//			return $workflow;
//		};
//
//		$resolver->setDefaults([
//			'empty_value'    => 'Don\'t change',
//
//			'choice_list'    => $choiceList,
//			'choice_current' => null,
//
//			'expanded'       => true,
//		]);
//
//		$resolver->setRequired([
//			'workflow'
//		]);
//
//		$resolver->setNormalizers([
//			'empty_data'     => $emptyDataNormalizer,
//
//			'choices'        => function () { return null; },
//			'multiple'       => function () { return false; }, // you can not be in multiple states ever so disabled
//
//			'workflow'       => $workflowNormalizer
//		]);
//
//		$resolver->setAllowedTypes([
//			'choice_current' => ['null', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition\\State'],
//			'workflow'       => ['string', 'Integrated\\Bundle\\WorkflowBundle\\Entity\\Definition']
//		]);
//	}
//
//	/**
//	 * @inheritdoc
//	 */
//	public function getParent()
//	{
//		return 'choice';
//	}

	/**
	 * @inheritdoc
	 */
	public function getName()
	{
		return 'integrated_workflow_state';
	}
} 