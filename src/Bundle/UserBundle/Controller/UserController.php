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

use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Integrated\Bundle\UserBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\UserBundle\Form\Type\UserFormType;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
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

        return $this->render('IntegratedUserBundle:user:index.html.twig', [
            'users' => $paginator,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createNewForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_user_index'));
            }

            if ($form->isValid()) {
                $user = $form->getData();

                $this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The user %s is created', $user->getUsername()));

                return $this->redirect($this->generateUrl('integrated_user_user_index'));
            }
        }

        return $this->render('IntegratedUserBundle:user:new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request)
    {
        $user = $this->getManager()->find($request->get('id'));

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createEditForm($user);

        if ($request->isMethod('put')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_user_index'));
            }

            if ($form->isValid()) {
                $this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to the user %s are saved', $user->getUsername()));

                return $this->redirect($this->generateUrl('integrated_user_user_index'));
            }
        }

        return $this->render('IntegratedUserBundle:user:edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $user = $this->getManager()->find($request->get('id'));

        if (!$user) {
            return $this->redirect($this->generateUrl('integrated_user_user_index')); // user is already gone
        }

        $form = $this->createDeleteForm($user);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_user_index'));
            }

            if ($form->isValid()) {
                $this->getManager()->remove($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The user %s is removed', $user->getUsername()));

                return $this->redirect($this->generateUrl('integrated_user_user_index'));
            }
        }

        return $this->render('IntegratedUserBundle:user:delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createNewForm()
    {
        $form = $this->createForm(
            UserFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_user_user_new'),
                'method' => 'POST',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'create' => ['type' => SubmitType::class, 'options' => ['label' => 'Create']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }

    /**
     * @param UserInterface $user
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(UserInterface $user)
    {
        $form = $this->createForm(
            UserFormType::class,
            $user,
            [
                'action' => $this->generateUrl('integrated_user_user_edit', ['id' => $user->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'save' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }

    /**
     * @param UserInterface $user
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm(UserInterface $user)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $user,
            [
                'action' => $this->generateUrl('integrated_user_user_delete', ['id' => $user->getId()]),
                'method' => 'DELETE',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }

    /**
     * @return UserManagerInterface
     *
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
