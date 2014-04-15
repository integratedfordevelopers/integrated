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

use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

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
class ProfileController extends Controller
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
			'users' => $paginator,
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
			'user_profile_new',
			null,
			[
				'action' => $this->generateUrl('integrated_user_profile_new'),
				'method' => 'POST',
			],
			[
				'create' => ['type' => 'submit', 'options' => ['label' => 'Create']],
				'cancel' => ['type' => 'button', 'options' => ['label' => 'Cancel']],
			]
		);

		if ($request->isMethod('post')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_user_profile_index'));
			}

			if ($form->isValid()) {
				$user = $form->getData();

				$this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The user %s is created', $user->getUsername()));

                return $this->redirect($this->generateUrl('integrated_user_profile_index'));
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
		$user = $this->getManager()->find($request->get('id'));

		if (!$user) {
			throw $this->createNotFoundException();
		}

		$form = $this->createForm(
			'user_profile_edit',
			$user,
			[
				'action' => $this->generateUrl('integrated_user_profile_edit', ['id' => $user->getId()]),
				'method' => 'PUT',
			],
			[
				'save' => ['type' => 'submit', 'options' => ['label' => 'Save']],
				'cancel' => ['type' => 'button', 'options' => ['label' => 'Cancel']],
			]
		);

		if ($request->isMethod('put')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_user_profile_index'));
			}

			if ($form->isValid()) {
				$this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to the user %s are saved', $user->getUsername()));

                return $this->redirect($this->generateUrl('integrated_user_profile_index'));
			}
		}

		return array(
			'user' => $user,
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
		$user = $this->getManager()->find($request->get('id'));

		if (!$user) {
			return $this->redirect($this->generateUrl('integrated_user_profile_index')); // user is already gone
		}

		$form = $this->createForm(
			'user_profile_delete',
			$user,
			[
				'action' => $this->generateUrl('integrated_user_profile_delete', ['id' => $user->getId()]),
				'method' => 'DELETE',
			],
			[
				'delete' => ['type' => 'submit', 'options' => ['label' => 'Delete']],
				'cancel' => ['type' => 'button', 'options' => ['label' => 'Cancel']],
			]
		);

		if ($request->isMethod('delete')) {
			$form->handleRequest($request);

			// check for back click else its a submit
			if ($form->get('cancel')->isClicked()) {
				return $this->redirect($this->generateUrl('integrated_user_profile_index'));
			}

			if ($form->isValid()) {
				$this->getManager()->remove($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The user %s is removed', $user->getUsername()));

                return $this->redirect($this->generateUrl('integrated_user_profile_index'));
			}
		}

		return array(
			'user' => $user,
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
	 * @return UserManagerInterface
	 * @throws \LogicException
	 */
	protected function getManager()
	{
		if (!$this->container->has('integrated_user.user.manager')) {
			throw new \LogicException('The UserBundle is not registered in your application.');
		}

		return $this->container->get('integrated_user.user.manager');
	}
}