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
use Integrated\Bundle\UserBundle\Model\UserInterface;
use Integrated\Bundle\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class TwoFactorController extends AbstractController
{
    /**
     * @var UserManagerInterface
     */
    private $manager;

    public function __construct(UserManagerInterface $manager, ContainerInterface $container)
    {
        $this->manager = $manager;
        $this->setContainer($container);
    }

    public function deleteAction(Request $request)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->manager->find($request->get('id'));

        if (!$user || !$user->isGoogleAuthenticatorEnabled()) {
            return $this->redirectToRoute('integrated_user_user_index');
        }

        $form = $this->createDeleteForm($user);

        if ($request->isMethod('delete')) {
            $form->handleRequest($request);

            if ($form->get('actions')->get('cancel')->isClicked()) {
                return $this->redirectToRoute('integrated_user_user_index');
            }

            if ($form->isValid()) {
                $user->setGoogleAuthenticatorSecret(null);

                $this->manager->persist($user);

                $translation = $this->get('translator')->trans('The two factor authenticator for user %name% is removed', ['%name%' => $user->getUsername()]);
                $this->get('braincrafted_bootstrap.flash')->success($translation);

                return $this->redirectToRoute('integrated_user_user_index');
            }
        }

        return $this->render('IntegratedUserBundle:two_factor:delete.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param UserInterface $user
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(UserInterface $user)
    {
        if (!$this->isGranted('ROLE_USER_MANAGER') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(
            DeleteFormType::class,
            $user,
            [
                'action' => $this->generateUrl('integrated_user_user_delete_authenticator', ['id' => $user->getId()]),
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
