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

use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\UserBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\UserBundle\Form\Type\ScopeFormType;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\ScopeManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilder;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class ScopeController extends Controller
{
    /**
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
            'scopes' => $paginator,
        );
    }

    /**
     * @Template
     *
     * @param Request $request
     * @return array | Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(
            ScopeFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_user_scope_new'),
                'method' => 'POST',
            ],
            [
                'create' => ['type' => SubmitType::class, 'options' => ['label' => 'Create']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ]
        );

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_scope_index'));
            }

            if ($form->isValid()) {
                $scope = $form->getData();

                $this->getManager()->persist($scope);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The scope %s is created', $scope->getName()));

                return $this->redirect($this->generateUrl('integrated_user_scope_index'));
            }
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @Template
     *
     * @param Scope $scope
     * @param Request $request
     * @return array | Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Scope $scope, Request $request)
    {
        if (!$scope) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(
            ScopeFormType::class,
            $scope,
            [
                'action' => $this->generateUrl('integrated_user_scope_edit', ['id' => $scope->getId()]),
                'method' => 'PUT',
            ],
            [
                'save' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ]
        );

        if ($request->isMethod('put')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_scope_index'));
            }

            if ($form->isValid()) {
                $this->getManager()->persist($scope);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to the scope %s are saved', $scope->getName()));

                return $this->redirect($this->generateUrl('integrated_user_scope_index'));
            }
        }

        return array(
            'scope' => $scope,
            'form' => $form->createView()
        );
    }

    /**
     * @Template
     *
     * @param Scope $scope
     * @param Request $request
     * @return array | Response
     */
    public function deleteAction(Scope $scope, Request $request)
    {
        if (!$scope || $scope->isAdmin()) {
            return $this->redirect($this->generateUrl('integrated_user_scope_index'));
        }

        $form = $this->createForm(
            DeleteFormType::class,
            $scope,
            [
                'action' => $this->generateUrl('integrated_user_scope_delete', ['id' => $scope->getId()]),
                'method' => 'DELETE',
            ],
            [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ]
        );

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirect($this->generateUrl('integrated_user_scope_index'));
            }

            /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
            $dm = $this->get('doctrine_mongodb')->getManager();
            if ($channels = $dm->getRepository(Channel::class)->findBy(['scope' => (string) $scope->getId()])) {
                $form->addError(
                    new FormError('This scope is in use by channels.')
                );
            } else {
                $this->getManager()->remove($scope);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The scope %s is removed', $scope->getName()));

                return $this->redirect($this->generateUrl('integrated_user_scope_index'));
            }
        }

        return [
            'scope' => $scope,
            'form' => $form->createView()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createForm($type, $data = null, array $options = [], array $buttons = [])
    {
        /** @var FormBuilder $form */
        $form = $this->container->get('form.factory')->createBuilder($type, $data, $options);

        if ($buttons) {
            $form->add('actions', FormActionsType::class, [
                'buttons' => $buttons
            ]);
        }

        return $form->getForm();
    }

    /**
     * @return ScopeManagerInterface
     */
    protected function getManager()
    {
        return $this->container->get('integrated_user.scope.manager');
    }
}
