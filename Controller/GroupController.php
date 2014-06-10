<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\UserBundle\Controller;

use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 *
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupController extends Controller
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
		/** @var $paginator \Knp\Component\Pager\Paginator */
		$paginator = $this->get('knp_paginator');
		$paginator = $paginator->paginate(
			$this->getManager()->findAll(),
			$request->query->get('page', 1),
			15
		);

		return array(
			'groups' => $paginator,
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
			'user_group_new',
			null,
			[
				'action' => $this->generateUrl('integrated_user_group_new'),
				'method' => 'POST',
			],
			[
				'create' => ['type' => 'submit', 'options' => ['label' => 'Create']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('post')) {
			$form->handleRequest($request);

			// check for cancel click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_user_group_index'));
			}

			if ($form->isValid()) {
				$user = $form->getData();

				$this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The group %s is created', $user->getName()));

                return $this->redirect($this->generateUrl('integrated_user_group_index'));
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
		$group = $this->getManager()->find($request->get('id'));

		if (!$group) {
			throw $this->createNotFoundException();
		}

		$form = $this->createForm(
			'user_group_edit',
			$group,
			[
				'action' => $this->generateUrl('integrated_user_group_edit', ['id' => $group->getId()]),
				'method' => 'PUT',
			],
			[
				'save' => ['type' => 'submit', 'options' => ['label' => 'Save']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('put')) {
			$form->handleRequest($request);

			// check for cancel click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_user_group_index'));
			}

			if ($form->isValid()) {
				$this->getManager()->persist($group);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to the group %s are saved', $group->getName()));

                return $this->redirect($this->generateUrl('integrated_user_group_index'));
			}
		}

		return array(
			'group' => $group,
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
		$group = $this->getManager()->find($request->get('id'));

		if (!$group) {
			return $this->redirect($this->generateUrl('integrated_user_group_index')); // group is already gone
		}

		$form = $this->createForm(
			'user_group_delete',
			$group,
			[
				'action' => $this->generateUrl('integrated_user_group_delete', ['id' => $group->getId()]),
				'method' => 'DELETE',
			],
			[
				'delete' => ['type' => 'submit', 'options' => ['label' => 'Delete']],
				'cancel' => ['type' => 'submit', 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
			]
		);

		if ($request->isMethod('delete')) {
			$form->handleRequest($request);

			// check for cancel click else its a submit
			if ($form->get('actions')->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_user_group_index'));
			}

			if ($form->isValid()) {
				$this->getManager()->remove($group);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The group %s is removed', $group->getName()));

                return $this->redirect($this->generateUrl('integrated_user_group_index'));
			}
		}

		return array(
			'group' => $group,
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

	/**
	 * @return GroupManagerInterface
	 * @throws \LogicException
	 */
	protected function getManager()
	{
		if (!$this->container->has('integrated_user.group.manager')) {
			throw new \LogicException('The UserBundle is not registered in your application.');
		}

		return $this->container->get('integrated_user.group.manager');
	}
}