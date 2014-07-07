<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\WorkflowBundle\Controller;

use Doctrine\ORM\EntityManager;

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\Type\DefinitionFormType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * Configuration class for ContentBundle
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowController extends Controller
{
	/**
	 *
	 *
	 * @Template
	 *
	 * @param Request $request
	 * @return array
	 */
	public function indexAction(Request $request)
	{
		/** @var EntityManager $em */
		$em = $this->getDoctrine()->getManager();

		/** @var $pager \Knp\Component\Pager\Paginator */
		$pager = $this->get('knp_paginator');
		$pager = $pager->paginate(
			$em->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')->createQueryBuilder('item'),
			$request->query->get('page', 1),
			15
		);

		return array(
			'pager' => $pager
		);
	}

	/**
	 *
	 *
	 * @Template
	 *
	 * @param Request $request
	 * @return array | Response
	 */
	public function newAction(Request $request)
	{
		$form = $this->createForm(
			'workflow_definition_new',
			null,
			[
				'action' => $this->generateUrl('integrated_workflow_new'),
				'method' => 'POST',
			],
			[
				'create' => ['type' => 'submit', 'options' => ['label' => 'Create']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('post')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_workflow_index'));
			}

			if ($form->isValid()) {
				$workflow = $form->getData();

				$manager = $this->getDoctrine()->getManager();
				$manager->persist($workflow);
				$manager->flush();

                return $this->redirect($this->generateUrl('integrated_workflow_index'));
			}
		}

		return array(
			'form' => $form->createView()
		);
	}

	/**
	 *
	 *
	 * @Template
	 *
	 * @param Request $request
	 * @return array | Response
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function editAction(Request $request)
	{
		$workflow = $this->getDoctrine()
				->getManager()
				->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')
				->find($request->get('id'));

		if (!$workflow) {
			throw $this->createNotFoundException();
		}

		$form = $this->createForm(
			'workflow_definition_edit',
			$workflow,
			[
				'action' => $this->generateUrl('integrated_workflow_edit', ['id' => $workflow->getId()]),
				'method' => 'PUT',
			],
			[
				'save' => ['type' => 'submit', 'options' => ['label' => 'Save']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('put')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_workflow_index'));
			}

			if ($form->isValid()) {
				$manager = $this->getDoctrine()->getManager();
				$manager->flush();

                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to the workflow %s are saved', $workflow->getName()));

                return $this->redirect($this->generateUrl('integrated_workflow_index'));
			}
		}

		return array(
			'workflow' => $workflow,
			'form' => $form->createView()
		);
	}

	/**
	 *
	 *
	 * @Template
	 *
	 * @param Request $request
	 * @return array | Response
	 */
	public function deleteAction(Request $request)
	{
		$workflow = $this->getDoctrine()
				->getManager()
				->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')
				->find($request->get('id'));

		if (!$workflow) {
			return $this->redirect($this->generateUrl('integrated_workflow_index')); // workflow is already gone
		}

		$form = $this->createForm(
			'workflow_definition_delete',
			$workflow,
			[
				'action' => $this->generateUrl('integrated_workflow_delete', ['id' => $workflow->getId()]),
				'method' => 'DELETE',
			],
			[
				'delete' => ['type' => 'submit', 'options' => ['label' => 'Delete']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('delete')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_workflow_index'));
			}

			if ($form->isValid()) {
				$manager = $this->getDoctrine()->getManager();
				$manager->remove($workflow);
				$manager->flush();

                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The workflow %s is removed', $workflow->getName()));

                return $this->redirect($this->generateUrl('integrated_workflow_index'));
			}
		}

		return array(
			'workflow' => $workflow,
			'form' => $form->createView()
		);
	}

	/**
	 * @inheritdoc
	 */
	public function createForm($type, $data = null, array $options = [], array $buttons = [])
	{
		/** @var FormBuilder $form */
		$form = $this->container->get('form.factory')->createBuilder($type, $data, $options);

		if ($buttons) {
			$form->add('actions', 'form_actions', [
				'buttons' => $buttons
			]);
		}

		return $form->getForm();
	}
}