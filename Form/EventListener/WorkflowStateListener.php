<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Form\EventListener;

use Integrated\Bundle\WorkflowBundle\Entity\Definition\State;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowStateListener implements EventSubscriberInterface
{
	/**
	 * @inheritdoc
	 */
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SET_DATA => 'onPreSetData',
			FormEvents::SUBMIT  => 'onSubmit'
		];
	}

	public function onPreSetData(FormEvent $event)
	{
		if (!$data = $event->getData()) {
			return;
		}

		if (!$data instanceof State) {
			return;
		}

		$form = $event->getForm();
		$form->get('current')->setData($data->getName());

		// replace the next with new field that has the current set

		$options = $form->get('next')->getConfig()->getOptions();
		$options['choice_current'] = $data;

		unset($options['choice_list']);

		$form->add('next', 'workflow_state_choice', $options);
		$form->get('next')->setData($data->getName());
	}

	public function onSubmit(FormEvent $event)
	{
		$form = $event->getForm()->get('next');

		if (!$data = $form->getData()) {
			$data = $form->getConfig()->getEmptyData();
		}

		$event->setData($data);
	}
}