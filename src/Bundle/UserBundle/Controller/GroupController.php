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
use Integrated\Bundle\UserBundle\Form\Type\GroupFormType;
use Integrated\Bundle\UserBundle\Model\GroupInterface;
use Integrated\Bundle\UserBundle\Model\GroupManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GroupController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /** @var $paginator \Knp\Component\Pager\Paginator */
        $paginator = $this->get('knp_paginator');
        $paginator = $paginator->paginate(
            $this->getManager()->findAll(),
            $request->query->get('page', 1),
            15
        );

        return $this->render('IntegratedUserBundle:group:index.html.twig', [
            'groups' => $paginator,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createNewForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_group_index'));
            }

            if ($form->isValid()) {
                $user = $form->getData();

                $this->getManager()->persist($user);
                $this->addFlash('success', sprintf('The group %s is created', $user->getName()));

                return $this->redirect($this->generateUrl('integrated_user_group_index'));
            }
        }

        return $this->render('IntegratedUserBundle:group:new.html.twig', [
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
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $group = $this->getManager()->find($request->get('id'));

        if (!$group) {
            throw $this->createNotFoundException();
        }

        $form = $this->createEditForm($group);

        if ($request->isMethod('put') || $request->isMethod('post')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_group_index'));
            }

            if ($form->isValid()) {
                $this->getManager()->persist($group);
                $this->addFlash('success', sprintf('The changes to the group %s are saved', $group->getName()));

                return $this->redirect($this->generateUrl('integrated_user_group_index'));
            }
        }

        return $this->render('IntegratedUserBundle:group:edit.html.twig', [
            'group' => $group,
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
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $group = $this->getManager()->find($request->get('id'));

        if (!$group) {
            return $this->redirect($this->generateUrl('integrated_user_group_index')); // group is already gone
        }

        $form = $this->createDeleteForm($group);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_group_index'));
            }

            if ($form->isValid()) {
                $this->getManager()->remove($group);
                $this->addFlash('success', sprintf('The group %s is removed', $group->getName()));

                return $this->redirect($this->generateUrl('integrated_user_group_index'));
            }
        }

        return $this->render('IntegratedUserBundle:group:delete.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createNewForm()
    {
        $form = $this->createForm(
            GroupFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_user_group_new'),
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
     * @param GroupInterface $group
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createEditForm(GroupInterface $group)
    {
        $form = $this->createForm(
            GroupFormType::class,
            $group,
            [
                'action' => $this->generateUrl('integrated_user_group_edit', ['id' => $group->getId()]),
                'method' => 'POST',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'create' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default', 'formnovalidate' => true]]],
            ],
        ]);

        return $form;
    }

    /**
     * @param GroupInterface $group
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createDeleteForm(GroupInterface $group)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $group,
            [
                'action' => $this->generateUrl('integrated_user_group_delete', ['id' => $group->getId()]),
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
     * @return GroupManagerInterface
     *
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
