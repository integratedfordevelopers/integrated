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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormInterface;
use Integrated\Bundle\FormTypeBundle\Form\Type\FormActionsType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\UserBundle\Model\Group;
use Integrated\Bundle\UserBundle\Model\User;
use Integrated\Bundle\WorkflowBundle\Entity\Definition;
use Integrated\Bundle\WorkflowBundle\Form\Type\DefinitionFormType;
use Integrated\Bundle\WorkflowBundle\Form\Type\DeleteFormType;
use Integrated\Bundle\WorkflowBundle\Utils\StateVisibleConfig;
use Integrated\Common\Security\PermissionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class WorkflowController extends AbstractController
{
    /**
     * Generate a list of workflow definitions.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var $pager \Knp\Component\Pager\Paginator */
        $pager = $this->get('knp_paginator');
        $pager = $pager->paginate(
            $em->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')->createQueryBuilder('item'),
            $request->query->get('page', 1),
            15
        );

        return $this->render('@IntegratedWorkflow/workflow/index.html.twig', ['pager' => $pager]);
    }

    /**
     * Create a new workflow definition.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        $form = $this->createNewForm();

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_workflow_index');
            }

            if ($form->isValid()) {
                $workflow = $form->getData();

                $manager = $this->getDoctrine()->getManager();
                $manager->persist($workflow);
                $manager->flush();

                return $this->redirectToRoute('integrated_workflow_index');
            }
        }

        return $this->render('@IntegratedWorkflow/workflow/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit a workflow definition.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function edit(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        /** @var Definition $workflow */
        $workflow = $this->getDoctrine()
            ->getManager()
            ->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')
            ->find($request->get('id'));

        if (!$workflow) {
            throw $this->createNotFoundException();
        }

        $form = $this->createEditForm($workflow);

        if ($request->isMethod('put')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_workflow_index');
            }

            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->flush();

                $this->addFlash('success', sprintf('The changes to the workflow %s are saved', $workflow->getName()));

                return $this->redirectToRoute('integrated_workflow_index');
            }
        }

        return $this->render('@IntegratedWorkflow/workflow/edit.html.twig', [
            'workflow' => $workflow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a workflow definition.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN']);

        /** @var Definition $workflow */
        $workflow = $this->getDoctrine()
            ->getManager()
            ->getRepository('Integrated\Bundle\WorkflowBundle\Entity\Definition')
            ->find($request->get('id'));

        if (!$workflow) {
            return $this->redirectToRoute('integrated_workflow_index'); // workflow is already gone
        }

        $form = $this->createDeleteForm($workflow);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_workflow_index');
            }

            if ($form->isValid()) {
                $manager = $this->getDoctrine()->getManager();
                $manager->remove($workflow);
                $manager->flush();

                $this->addFlash('success', sprintf('The workflow %s is removed', $workflow->getName()));

                return $this->redirectToRoute('integrated_workflow_index');
            }
        }

        return $this->render('@IntegratedWorkflow/workflow/delete.html.twig', [
            'workflow' => $workflow,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function changeState(Request $request)
    {
        $stateId = $request->get('state');

        $isDefaultState = false;

        if (empty($stateId)) {
            $workflowId = $request->get('workflow');
            $repository = $this->getDoctrine()->getRepository(Definition::class);
            $workflow = $repository->find($workflowId);
            $state = $workflow->getDefault();

            $isDefaultState = true;
        } else {
            $repository = $this->getDoctrine()->getRepository(Definition\State::class);
            $state = $repository->find($stateId);
        }

        if (!$state) {
            return new JsonResponse(['users' => [], 'fields' => []]);
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

        $permissionObject = false;
        if (\count($state->getPermissions()) > 0) {
            $permissionObject = $state;
        } else {
            //permissions inherited from content type
            $contentType = $this->get('doctrine_mongodb.odm.document_manager')->getRepository(ContentType::class)->find($request->get('contentType'));
            if ($contentType && \count($contentType->getPermissions()) > 0) {
                $permissionObject = $contentType;
            }
        }

        //use workflow permissions
        if ($permissionObject) {
            foreach ($permissionObject->getPermissions() as $permission) {
                if ($permission->getMask() >= PermissionInterface::WRITE) {
                    $group = $permission->getGroup();
                    $groups[] = $group;

                    if (\in_array($group, $currentUserGroups)) {
                        $currentUserCanWrite = true;
                    }
                }
            }
        }

        /** @var EntityRepository $userRepository */
        $userRepository = $this->get('integrated_user.user.manager.doctrine')->getRepository();
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $userRepository->createQueryBuilder('u');

        $queryBuilder->join('u.scope', 'us');
        $queryBuilder->where('us.admin = 1');

        if ($permissionObject && (!$isDefaultState || !$currentUserCanWrite)) {
            $queryBuilder->join('u.groups', 'ug');
            $queryBuilder->where('ug.id IN (:groups)')->setParameter('groups', $groups);
        }

        $users = [];
        /** @var User $item */
        foreach ($queryBuilder->getQuery()->getResult() as $item) {
            if ($item->getRelation() instanceof Person) {
                $users[] = [
                    'id' => $item->getId(),
                    'name' => $item->getRelation()->getFirstname().' '.$item->getRelation()->getLastName(),
                    ];
            } else {
                $users[] = [
                    'id' => $item->getId(),
                    'name' => $item->getUsername(),
                ];
            }
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

        usort($users, function ($a, $b) {
            return $a['name'] > $b['name'];
        });

        return new JsonResponse(['users' => $users, 'fields' => $fieldsCodes]);
    }

    /**
     * @return FormInterface
     */
    protected function createNewForm()
    {
        $form = $this->createForm(
            DefinitionFormType::class,
            null,
            [
                'action' => $this->generateUrl('integrated_workflow_new'),
                'method' => 'POST',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'create' => ['type' => SubmitType::class, 'options' => ['label' => 'Create']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ],
        ]);

        return $form;
    }

    /**
     * @param Definition $workflow
     *
     * @return FormInterface
     */
    protected function createEditForm(Definition $workflow)
    {
        $form = $this->createForm(
            DefinitionFormType::class,
            $workflow,
            [
                'action' => $this->generateUrl('integrated_workflow_edit', ['id' => $workflow->getId()]),
                'method' => 'PUT',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'save' => ['type' => SubmitType::class, 'options' => ['label' => 'Save']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ],
        ]);

        return $form;
    }

    /**
     * @param Definition $workflow
     *
     * @return FormInterface
     */
    protected function createDeleteForm(Definition $workflow)
    {
        $form = $this->createForm(
            DeleteFormType::class,
            $workflow,
            [
                'action' => $this->generateUrl('integrated_workflow_delete', ['id' => $workflow->getId()]),
                'method' => 'DELETE',
            ]
        );

        $form->add('actions', FormActionsType::class, [
            'buttons' => [
                'delete' => ['type' => SubmitType::class, 'options' => ['label' => 'Delete']],
                'cancel' => ['type' => SubmitType::class, 'options' => ['label' => 'Cancel', 'attr' => ['type' => 'default']]],
            ],
        ]);

        return $form;
    }
}
