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

use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Integrated\Bundle\UserBundle\Model\Group;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\Type\DefinitionFormType;
use Integrated\Bundle\WorkflowBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\WorkflowBundle\Utils\StateVisibleConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowController extends Controller
{
    /**
     * Generate a list of workflow definitions.
     *
     * @param Request $request
     *
     * @return Response
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

        return $this->render('IntegratedWorkflowBundle:Workflow:index.html.twig', ['pager' => $pager]);
    }

    /**
     * Create a new workflow definition.
     *
     * @param Request $request
     *
     * @return array | Response
     */
    public function newAction(Request $request)
    {
        $form = $this->createForm(
            DefinitionFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_workflow_new'),
                'method' => 'POST',
            ],
            [
                'create' => ['type' => SubmitType::class, 'options' => ['label' => 'Create']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
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

        return $this->render('IntegratedWorkflowBundle:Workflow:new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit a workflow definition.
     *
     * @param Request $request
     *
     * @return array | Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request)
    {
        /** @var Definition $workflow */
        $workflow = $this->getDoctrine()
            ->getManager()
            ->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')
            ->find($request->get('id'));

        if (!$workflow) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(
            DefinitionFormType::class,
            $workflow,
            [
                'action' => $this->generateUrl('integrated_workflow_edit', ['id' => $workflow->getId()]),
                'method' => 'PUT',
            ],
            [
                'save' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
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

        return $this->render('IntegratedWorkflowBundle:Workflow:edit.html.twig', [
            'workflow' => $workflow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a workflow definition.
     *
     * @param Request $request
     *
     * @return array | Response
     */
    public function deleteAction(Request $request)
    {
        /** @var Definition $workflow */
        $workflow = $this->getDoctrine()
            ->getManager()
            ->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')
            ->find($request->get('id'));

        if (!$workflow) {
            return $this->redirect($this->generateUrl('integrated_workflow_index')); // workflow is already gone
        }

        $form = $this->createForm(
            DeleteFormType::class,
            $workflow,
            [
                'action' => $this->generateUrl('integrated_workflow_delete', ['id' => $workflow->getId()]),
                'method' => 'DELETE',
            ],
            [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
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

        return $this->render('IntegratedWorkflowBundle:Workflow:delete.html.twig', [
            'workflow' => $workflow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeStateAction(Request $request)
    {
        $stateId = $request->get('state');

        $isDefaultState = false;
        if (empty($stateId)) {
            $workflowId = $request->get('workflow');
            $repository = $this->getDoctrine()->getRepository('IntegratedWorkflowBundle:Definition');
            $workflow = $repository->find($workflowId);
            $state = $workflow->getDefault();

            $isDefaultState = true;
        } else {
            $repository = $this->getDoctrine()->getRepository('IntegratedWorkflowBundle:Definition\State');
            $state = $repository->find($stateId);
        }

        /** @var User $currentUser */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $currentUserGroups = [];
        /** @var Group $group */
        foreach ($currentUser->getGroups() as $group) {
            $currentUserGroups[] = $group->getId();
        }

        $groups = [];
        $currentUserCanWrite = false;
        foreach ($state->getPermissions() as $permission) {
            if ($permission->getMask() >= Definition\Permission::WRITE) {
                $group = $permission->getGroup();
                $groups[] = $group;

                if (in_array($group, $currentUserGroups)) {
                    $currentUserCanWrite = true;
                }
            }
        }

        /** @var EntityRepository $userRepository */
        $userRepository = $this->get('integrated_user.user.manager.doctrine')->getRepository();
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $userRepository->createQueryBuilder('u');

        if (!$isDefaultState || !$currentUserCanWrite) {
            $queryBuilder->join('u.groups', 'ug');
            $queryBuilder->where('ug.id IN (:groups)')->setParameter('groups', $groups);
        }

        $users = [];
        /** @var User $item */
        foreach ($queryBuilder->getQuery()->getResult() as $item) {
            $users[$item->getId()] = $item->getUsername();
        }

        $fieldsCodes = [
            'comment' => [
                'required' => $state->getComment() == StateVisibleConfig::REQUIRED,
                'disabled' => $state->getComment() == StateVisibleConfig::DISABLED,
            ],
            'assigned-choice' => [
                'required' => $state->getAssignee() == StateVisibleConfig::REQUIRED,
                'disabled' => $state->getAssignee() == StateVisibleConfig::DISABLED,
            ],
            'deadline' => [
                'required' => $state->getDeadline() == StateVisibleConfig::REQUIRED,
                'disabled' => $state->getDeadline() == StateVisibleConfig::DISABLED,
            ],
        ];

        return new JsonResponse(['users' => $users, 'fields' => $fieldsCodes]);
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
                'buttons' => $buttons,
            ]);
        }

        return $form->getForm();
    }
}
