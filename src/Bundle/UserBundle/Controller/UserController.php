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

use Integrated\Bundle\IntegratedBundle\Controller\AbstractController;
use Integrated\Bundle\UserBundle\Provider\FilterQueryProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormInterface;
use Integrated\Bundle\UserBundle\Form\Type\DeleteFormType;
use Braincrafted\Bundle\BootstrapBundle\Form\Type\FormActionsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Integrated\Bundle\UserBundle\Form\Type\UserFilterType;
use Integrated\Bundle\UserBundle\Form\Type\UserFormType;
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class UserController extends AbstractController
{
    /**
     * @var FilterQueryProvider
     */
    private $filterQueryProvider;

    public function __construct(FilterQueryProvider $filterQueryProvider)
    {
        $this->filterQueryProvider = $filterQueryProvider;
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

        $data = $request->query->get('integrated_user_filter');

        $users = $this->filterQueryProvider->getUsers($data);

        $facetFilter = $this->createForm(UserFilterType::class, null, [
            'data' => $data,
        ]);
        $facetFilter->handleRequest($request);

        $pagination = $this->getPaginator()->paginate(
            $users,
            $request->query->get('page', 1),
            15
        );

        return $this->render('@IntegratedUser/user/index.html.twig', [
            'users' => $pagination,
            'facetFilter' => $facetFilter->createView(),
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

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_user_index');
            }

            if ($form->isValid()) {
                $user = $form->getData();

                $this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The user %s is created', $user->getUsername()));

                return $this->redirectToRoute('integrated_user_user_index');
            }
        }

        return $this->render('@IntegratedUser/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function edit(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getManager()->find($request->get('id'));

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $form = $this->createEditForm($user);

        if ($request->isMethod('put')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_user_index');
            }

            if ($form->isValid()) {
                $this->getManager()->persist($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The changes to the user %s are saved', $user->getUsername()));

                return $this->redirectToRoute('integrated_user_user_index');
            }
        }

        return $this->render('@IntegratedUser/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getManager()->find($request->get('id'));

        if (!$user) {
            return $this->redirectToRoute('integrated_user_user_index'); // user is already gone
        }

        $form = $this->createDeleteForm($user);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            // check for back click else its a submit
            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_user_index');
            }

            if ($form->isValid()) {
                $this->getManager()->remove($user);
                $this->get('braincrafted_bootstrap.flash')->success(sprintf('The user %s is removed', $user->getUsername()));

                return $this->redirectToRoute('integrated_user_user_index');
            }
        }

        return $this->render('@IntegratedUser/user/delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return FormInterface
     */
    protected function createNewForm()
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

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
     * @return FormInterface
     */
    protected function createEditForm(UserInterface $user)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

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
     * @return FormInterface
     */
    protected function createDeleteForm(UserInterface $user)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

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
