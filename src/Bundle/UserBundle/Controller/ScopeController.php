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

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormInterface;
use Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;
use Integrated\Bundle\IntegratedBundle\Controller\AbstractController;
use Integrated\Bundle\UserBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\UserBundle\Form\Type\ScopeFormType;
use Integrated\Bundle\UserBundle\Model\Scope;
use Integrated\Bundle\UserBundle\Model\ScopeManagerInterface;
use Integrated\Bundle\UserBundle\Model\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michael Jongman <michael@e-active.nl>
 */
class ScopeController extends AbstractController
{
    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ScopeManagerInterface
     */
    private $scopeManager;

    public function __construct(DocumentManager $documentManager, EntityManager $entityManager, ScopeManagerInterface $scopeManager)
    {
        $this->documentManager = $documentManager;
        $this->entityManager = $entityManager;
        $this->scopeManager = $scopeManager;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $paginator = $this->getPaginator()->paginate(
            $this->scopeManager->findAll(),
            $request->query->get('page', 1),
            15
        );

        return $this->render('@IntegratedUser/scope/index.html.twig', [
            'scopes' => $paginator,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createNewForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_scope_index');
            }

            if ($form->isValid()) {
                $scope = $form->getData();

                $this->scopeManager->persist($scope);
                $this->addFlash('success', sprintf('The scope %s is created', $scope->getName()));

                return $this->redirectToRoute('integrated_user_scope_index');
            }
        }

        return $this->render('@IntegratedUser/scope/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Scope   $scope
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function edit(Scope $scope, Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createEditForm($scope);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_scope_index');
            }

            if ($form->isValid()) {
                $this->scopeManager->persist($scope);
                $this->addFlash('success', sprintf('The changes to the scope %s are saved', $scope->getName()));

                return $this->redirectToRoute('integrated_user_scope_index');
            }
        }

        return $this->render('@IntegratedUser/scope/edit.html.twig', [
            'scope' => $scope,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Scope   $scope
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Scope $scope, Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if (!$scope || $scope->isAdmin()) {
            return $this->redirectToRoute('integrated_user_scope_index');
        }

        $form = $this->createDeleteForm($scope);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // check for cancel click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_scope_index');
            }

            $hasRelations = false;

            if ($channels = $this->documentManager->getRepository(Channel::class)->findBy(['scope' => (string) $scope->getId()])) {
                $form->addError(
                    new FormError('This scope is in use by channels.')
                );

                $hasRelations = true;
            }

            if ($users = $this->entityManager->getRepository(User::class)->findBy(['scope' => $scope])) {
                $form->addError(
                    new FormError('This scope is in use by users.')
                );

                $hasRelations = true;
            }

            if (false === $hasRelations) {
                $this->scopeManager->remove($scope);
                $this->addFlash('success', sprintf('The scope %s is removed', $scope->getName()));

                return $this->redirectToRoute('integrated_user_scope_index');
            }
        }

        return $this->render('@IntegratedUser/scope/delete.html.twig', [
            'scope' => $scope,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return FormInterface
     */
    protected function createNewForm()
    {
        $form = $this->createForm(
            ScopeFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_user_scope_new'),
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
     * @param Scope $scope
     *
     * @return FormInterface
     */
    protected function createEditForm(Scope $scope)
    {
        $form = $this->createForm(
            ScopeFormType::class,
            $scope,
            [
                'action' => $this->generateUrl('integrated_user_scope_edit', ['id' => $scope->getId()]),
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
     * @param Scope $scope
     *
     * @return FormInterface
     */
    protected function createDeleteForm(Scope $scope)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $scope,
            [
                'action' => $this->generateUrl('integrated_user_scope_delete', ['id' => $scope->getId()]),
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
}
